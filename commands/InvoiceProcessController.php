<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 24/10/19
 * Time: 15:13
 */
namespace app\commands;

use app\modules\sale\models\InvoiceProcess;
use app\modules\sale\modules\invoice\components\Invoice;
use yii\console\Controller;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use Yii;
use app\modules\sale\models\search\BillSearch;

class InvoiceProcessController extends Controller
{

    public function actionControlInvoiceProcess()
    {
        $pending_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CREATE_BILLS);
        $pending_close_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS);

        if($pending_invoice_process) {
            try {
                if(Yii::$app->mutex_create_bills->acquire('mutex')) {
                    $this->invoiceAll($pending_invoice_process);
                }
            } catch (\Exception $ex) {
                echo "ERROR__________". $ex->getTraceAsString();
                \Yii::info('ERROR ________________ ' .$ex->getTraceAsString(), 'facturacion-creacion');
            }
        }

        if($pending_close_invoice_process) {
            echo "entra a cerrar \n";
            try {
                if(Yii::$app->mutex_close_bills->acquire('mutex')) {
                    $this->closePendingBills($pending_close_invoice_process);
                }
            } catch (\Exception $ex) {
                echo "ERROR__________". $ex->getTraceAsString();
                \Yii::info('ERROR ________________ ' .$ex->getTraceAsString(), 'facturacion-cerrado');
            }
        }
    }


    public function invoiceAll(InvoiceProcess $invoice_process)
    {
        $cti = new ContractToInvoice();
        echo 'company_id ' .$invoice_process->company_id ."\n";
        echo 'bill_type_id '.$invoice_process->bill_type_id . "\n";
        echo 'period ' . $invoice_process->period . "\n";
        echo 'bill_observation '.$invoice_process->observation ."\n";
        $cti->invoiceAll(
            [
                'ContractSearch' => [
                    'company_id' => $invoice_process->company_id,
                    'bill_type_id' => $invoice_process->bill_type_id,
                    'period' => $invoice_process->period,
                ],
                'bill_observation' => $invoice_process->observation
            ]
        );
    }

    public function closePendingBills($invoice_process)
    {
        $i = 1;
        $searchModel = new BillSearch();
        $query = $searchModel->searchPendingToClose([
            'BillSearch' => [
                'company_id' => $invoice_process->company_id,
                'bill_type_id' => $invoice_process->bill_type_id,
                'period' => $invoice_process->period,
            ]
        ]);

        $total = $query->count();
//        $retMessages = [];

        echo "total $total \n";
        Yii::$app->cache->set('_invoice_close_', [
            'total' => $total,
            'qty' => $i
        ]);


        foreach ($query->batch() as $bills) {
            foreach ($bills as $bill) {
                echo "$bill->bill_id \n";
                $bill->verifyNumberAndDate();
                $bill->close();

//                $messages = Yii::$app->session->getAllFlashes();
//                $fn = function ($messages) {
//                    $rtn = [];
//                    if(is_array($messages)) {
//                        foreach ($messages as $message) {
//                            $rtn[] = Yii::t('afip', $message);
//                        }
//                    }
//
//                    return $rtn;
//                };
//                foreach ($messages as $key => $message) {
//                    $retMessages[$key][] = ($bill->customer ? $bill->customer->name : '') . " - " . Yii::t('app', 'Bill') . ' ' .
//                        Yii::t('app', 'Status') . ' ' . Yii::t('app', $bill->status) . ' - ' . implode('<br/>', $fn($message));
//                }

                Yii::$app->cache->set('_invoice_close_', [
                    'total' => $total,
                    'qty' => $i
                ]);
                $i++;
            }
        }

        InvoiceProcess::endProcess(InvoiceProcess::TYPE_CLOSE_BILLS);
    }
}