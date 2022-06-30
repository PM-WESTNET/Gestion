<?php

namespace app\commands;

use yii\console\Controller;
use Yii;
use app\modules\westnet\models\Vendor;
use app\modules\westnet\models\VendorLiquidation;
use app\modules\westnet\models\VendorLiquidationProcess;
use app\modules\sale\models\Product;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\alertsbot\controllers\TelegramController;


class VendorLiquidationController extends Controller{

    public function actionLiquidationByLot(){
        $model = VendorLiquidationProcess::find()->where(['status' => 'pending'])->one();

        if($model) { // if liquidation ANY 'pending' vendor liquidation is found ...
            try {
                if(Yii::$app->mutex->acquire('mutex_liquidate_vendors')) { // only one liquidation can be active at a time
                    $this->stdout("\nLiquidation By Lot initiated.\n"); // you can catch this output by using >> as an extra parameter via command line interface
                    $this->stdout("Vendor Liquidation Process ID: ".$model->vendor_liquidation_process_id."\n");
                    
                    $model->setProcessStartTime(); // sets the time at which the liquidation was started

                    $this->LiquidationProcess($model); // liquidates all pending items related to the process
                    $model->status = 'success'; // update status
                    $model->setProcessFinishTime(); // sets the time at which the liquidation was started
                    $model->setTimeSpent(); // calculates and sets the time spent on the process
                    
                    if($model->save()){
                        $this->stdout("Process model saved succesfully.\n");
                        $vendor_liquidations = VendorLiquidation::find()->where(['vendor_liquidation_process_id' => $model->vendor_liquidation_process_id])->all();
            
                        foreach ($vendor_liquidations as $key => $value) {
                            if($value->status == "cancelled" || empty($value->total)) $value->delete();
                        }
                        $this->stdout("Vendor Liquidation Finished\n");
                    }else{
                        $this->stdout("Err. couldnt save model.\n");
                        $this->stdout("Error summary: \n");
                        $this->stdout(var_dump($model->getErrorSummary(true)));
                        $this->stdout(var_dump($model->period,$model->finish_time,$model->start_time));
                    }
                    
                }
            } catch (\Exception $ex) {
                $err_msg="ERROR__________". 'Linea '.$ex->getLine()."\n" .'Archivo '.$ex->getFile() ."\n" .$ex->getMessage() ."\n" .$ex->getTraceAsString()."\n";
                $this->stdout($err_msg);
                //\Yii::info('ERROR ________________ ' . $ex->getMessage() ."\n" .$ex->getTraceAsString(), 'facturacion-creacion');

                // send error to telegram
                TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: vendor-liquidation/liquidation-by-lot ****', $ex);
            }
        }else{
            //$this->stdout("\nNo Liquidation Found.\n");
        }
    }


    /**
     * Liquidacion por lotes
     * @return mixed
     */
    public function LiquidationProcess($model)
    {
        $vendor_liquidations = VendorLiquidation::FindVendorLiquidationPendingAll($model->vendor_liquidation_process_id);

        foreach ($vendor_liquidations as $liquidation) {
            $contractDetails = $model->findContractsDetailsSQL($liquidation['vendor_id']);
            $this->stdout( "Liquidation " . $liquidation['vendor_liquidation_id'] . "\n" ); 
            $state = 'cancelled'; // default state is 'cancelled'
            if ($contractDetails) {
                $this->liquidateVendorItems($liquidation, $contractDetails); // liquidation of vendor items

                $state = 'success'; // change state to 'success'
            }
            VendorLiquidation::UpdateStatusVendorLiquidation($state, $liquidation['vendor_liquidation_id']);
        }
    }


    /**
     * Genera un item de liquidacion
     * @param type $liq
     * @param type $detail
     */
    private function liquidateVendorItems($liq, $details)
    {
        $vendor_liquidation_items = [];
        
        // get current company name value
        $currentCompanyOwner = isset(Yii::$app->params['gestion_owner_company']) ? Yii::$app->params['gestion_owner_company'] : null;
        if(is_null($currentCompanyOwner)){
            $this->stdout('Company owner is not setted in the Params file.');
        }

        foreach($details as $detail){
            $price = Product::findOne(['product_id' => $detail['product_id']])->getPriceFromDate($detail['date'])->one();
            if(!isset($price->finalPrice)){
                $this->stdout( "Product ID: " .$detail['product_id']. "\n" ); 
            }
            //Si el precio del producto es mayor a 0
            if($price->finalPrice > 0){
                $contract = Contract::findOne(['contract_id' => $detail['contract_id']]);
                $customer = $contract->customer;

                //Por problemas con datos migrados, agregamos esta cond de customer_id > 22200
                if($contract->status == 'active' && $this->hasPayedFirstBill($customer, $detail, $liq)){
                    
                    // jumps all customers below ID:22200 when the company is westnet.
                    if($currentCompanyOwner == 'westnet'){
                        if(!($customer->customer_id > 22200)) continue;
                    }

                    $product = Product::findOne(['product_id' => $detail['product_id']]);
                    $amount = 0.0;

                    //Si es un plan, la comision se calcula por vendedor (VendorCommission
                    if($product->type == 'plan'){
                        $vendor = Vendor::findOne(['vendor_id' => $liq['vendor_id']]);
                        $amount = $vendor->commission->calculateCommission($price->finalPrice);

                    //Si es un producto, la comision se calcula por producto (solo si el producto tiene asociada una comision)
                    }else{

                        if(isset($product->commission)){
                            $amount = $product->commission->calculateCommission($price->finalPrice);
                        }
                    }

                    $liqItem = [
                        'contract_detail_id' => $detail['contract_detail_id'],
                        'description' => $product->name,
                        'vendor_liquidation_id' => $liq['vendor_liquidation_id'],
                        'amount' => $amount
                    ];

                    array_push($vendor_liquidation_items, $liqItem);
                }
            }
        }

        VendorLiquidation::batchInsertLiquidationItems($vendor_liquidation_items);
    }

    private function hasPayedFirstBill($customer, $contractDetail, $liquidation)
    {

        $paymentModel = new PaymentSearch();
        $paymentModel->customer_id = $customer->customer_id;
        
        //Consideramos el saldo hasta el ultimo dia habil del mes que se esta liquidando
        $toDate = (new \DateTime( $liquidation['period'] ))->format('Y-m-t');
        $fromDate = (new \DateTime( $contractDetail['date'] ))->format('Y-m-d');

        //Facturas desde la fecha de contrato; una factura anterior no incluiria este contrato
        $billed = $paymentModel->accountTotalCredit($fromDate, $toDate);
        
        //Si no se ha facturado nada, devolvemos false
        if($billed == 0){
            return false;
        }
        
        //Pagos desde la fecha de contrato, porque el pago puede existir antes de la factura
        $payed = $paymentModel->accountPayed($fromDate, $toDate);
        
        if(($billed - $payed) < Yii::$app->params['account_tolerance']){
            return true;
        }else{
            return false;
        }

    }
} 
