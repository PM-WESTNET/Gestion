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


class VendorLiquidationController extends Controller{

    public function actionLiquidationByLot(){
        $model = VendorLiquidationProcess::find()->where(['status' => 'pending'])->one();
        $this->LiquidationProcess($model);

        $model->status = 'success';
        $model->save(false);

        $vendor_liquidations = VendorLiquidation::find()->where(['vendor_liquidation_process_id' => $model->vendor_liquidation_process_id])->all();

        foreach ($vendor_liquidations as $key => $value) {
	    if($value->status == "cancelled" || empty($value->total))
            	$value->delete();
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
            echo "Liquidation " . $liquidation['vendor_liquidation_id'] . "\n"; 

            if ($contractDetails) {
                $this->liquidateVendorItems($liquidation, $contractDetails);
                VendorLiquidation::UpdateStatusVendorLiquidation('success', $liquidation['vendor_liquidation_id']);

            }else{
                VendorLiquidation::UpdateStatusVendorLiquidation('cancelled', $liquidation['vendor_liquidation_id']);
            }
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

        foreach($details as $detail){
            $price = Product::findOne(['product_id' => $detail['product_id']])->getPriceFromDate($detail['date'])->one();

            //Si el precio del producto es mayor a 0
            if($price->finalPrice > 0){
                $contract = Contract::findOne(['contract_id' => $detail['contract_id']]);
                $customer = $contract->customer;

                //Por problemas con datos migrados, agregamos esta cond de customer_id > 22200
                if($customer->customer_id > 22200 && $contract->status == 'active' && $this->hasPayedFirstBill($customer, $detail, $liq)){

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
