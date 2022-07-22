<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 23/10/19
 * Time: 16:20
 */

namespace app\commands;


use app\modules\config\models\Config;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\westnet\models\Connection;

use DateTime;
use yii\console\Controller;
use app\modules\alertsbot\controllers\TelegramController;

class PaymentExtensionController extends Controller
{

    /**
     * Se encarga de activar los items de contratos de extensiones de pagos que se encuentran en estado borrador
     *
     */
    public function actionActivePendingContractDetails()
    {
        try{
            $product_id = Config::getValue('extend_payment_product_id');

            $contracts = Contract::find()
                ->innerJoin('connection conn', 'conn.contract_id=contract.contract_id')
                ->innerJoin('contract_detail cd', 'cd.contract_id=contract.contract_id')
                ->andWhere(['OR',
                    [
                        'contract.status' => Contract::STATUS_ACTIVE,
                        'conn.status_account' => Connection::STATUS_ACCOUNT_FORCED
                    ],
                    [
                        'contract.status' => Contract::STATUS_ACTIVE,
                        'conn.status_account' => Connection::STATUS_ENABLED
                    ]
                ])
                ->andWhere([
                    'cd.status' => ContractDetail::STATUS_DRAFT,
                    'cd.product_id' => $product_id
                ])
                ->all();

            echo 'Contratos encontrados sin activar: ' . count($contracts);
            echo "\n";
            foreach ($contracts as $contract) {
                echo 'Contrato: '. $contract->contract_id;
                echo "\n";
                $details = $contract->getContractDetails()
                    ->andWhere([
                        'status' => Contract::STATUS_DRAFT,
                        'product_id' => $product_id
                    ])
                    ->all();

                /**@var ContractDetail $detail **/
                foreach ($details as $detail) {
                    echo 'Detalle: '. $detail->contract_detail_id;
                    echo "\n";
                    $period = (new DateTime($detail->from_date));
                    if (strtotime($detail->from_date) > time() && $detail->canAddProductToInvoice($period)) {
                        //Solicito el precio activo en funcion del contrato de cliente (aplica reglas de negocio)
                        $activePrice = $detail->product->getActivePrice($detail)->one();

                        // Es sin plan de pago, genero para el preiodo que coresponde y sigo.
                        $ptb = $this->createProductToInvoice([
                            'contract_detail_id' => $detail->contract_detail_id,
                            'funding_plan_id' => $detail->funding_plan_id,
                            'date' => (new DateTime('now'))->format('d-m-Y'),
                            'amount' => $activePrice->net_price,
                            'status' => Contract::STATUS_ACTIVE,
                            'period' => $period->format('d-m-Y'),
                            'qty' => $detail->count,
                            'discount_id' => ($detail->discount ? $detail->discount->discount_id : null )
                        ]);
                        if ($ptb->save(true)) {
                            $detail->status = ContractDetail::STATUS_ACTIVE;
                            $detail->to_date = $period->modify('first day of next month')->modify('-1 day')->format('Y-m-d');
                            $detail->updateAttributes(['status', 'to_date']);
                            echo "Producto A facturar creado con éxito";
                            echo "\n";
                        }else{
                            echo "no se pudo activar el detalle. Producto a facturar no creado";
                            echo "\n";
                            echo print_r($ptb->getErrors(), 1);
                            echo "\n";
                        }
                    }
                }
            }

        }
        catch(\Exception $ex){
            // send error to telegram
            TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: payment-extension/active-pending-contract-details ****', $ex);
        }
    }

    private function createProductToInvoice($params)
    {
        $ptb = new ProductToInvoice();
        $ptb->contract_detail_id = $params['contract_detail_id'];
        $ptb->funding_plan_id = $params['funding_plan_id'];
        $ptb->date = $params['date'];
        $ptb->amount = $params['amount'];
        $ptb->status = Contract::STATUS_ACTIVE;
        $ptb->period = $params['period'];
        $ptb->qty = $params['qty'];
        if(array_key_exists('discount_id', $params)){
            $ptb->discount_id = $params['discount_id'];
        }
        return $ptb;
    }

}