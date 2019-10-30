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


class InvoiceProcessController extends Controller
{

    public function actionControlInvoiceProcess()
    {
        $pending_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CREATE_BILLS);

        if($pending_invoice_process) {
            try {
                if(Yii::$app->mutex->acquire('mutex')) {
                    $this->invoiceAll($pending_invoice_process);
                }
            } catch (\Exception $ex) {
                echo 'EROOR _________' .$ex->getTraceAsString();
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
}