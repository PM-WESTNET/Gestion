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
    public function init()
    {
        Yii::setAlias('@webroot', __DIR__.'/../web');
        parent::init();
    }

    public function actionControlInvoiceProcess()
    {
        $pending_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CREATE_BILLS);
        $pending_close_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS);

        if($pending_invoice_process) {
            try {
                if(Yii::$app->mutex_create_bills->acquire('mutex')) {
                    Yii::$app->cache->set('_invoice_create_errors', []);
                    $this->invoiceAll($pending_invoice_process);
                }
            } catch (\Exception $ex) {
                echo "ERROR__________". $ex->getTraceAsString();
                \Yii::info('ERROR ________________ ' .$ex->getTraceAsString(), 'facturacion-creacion');
            }
        }

        if($pending_close_invoice_process) {
            try {
                if(Yii::$app->mutex_close_bills->acquire('mutex')) {
                   Yii::$app->cache->set('_invoice_close_errors' ,[]);
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

        Yii::$app->cache->set('_invoice_close_', [
            'total' => $total,
            'qty' => $i
        ]);

        foreach ($query->batch() as $bills) {
            foreach ($bills as $bill) {
                echo "$bill->bill_id \n";
                $bill->verifyNumberAndDate();
                if($bill->close()){
                    Yii::$app->cache->set('_invoice_close_', [
                        'total' => $total,
                        'qty' => $i
                    ]);
                    $i++;
                }
            }
        }

        InvoiceProcess::endProcess(InvoiceProcess::TYPE_CLOSE_BILLS);
    }
}