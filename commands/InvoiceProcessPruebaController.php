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
use app\modules\sale\modules\contract\components\ContractToInvoicePrueba;
use Yii;
use app\modules\sale\models\search\BillSearch;

class InvoiceProcessPruebaController extends Controller
{
    public function init()
    {
        Yii::setAlias('@webroot', __DIR__.'/../web');
        parent::init();
    }

    /**
     * Inicia y controla el proceso de creacion de los comprobantes
     */
    public function actionControlCreationInvoiceProcess()
    {
        $this->invoiceAll();
    }

    /**
     * Inicia y controla el proceso de cierre de los comprobantes
     */
    public function actionControlCloseInvoiceProcess()
    {
        $pending_close_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS);

        if($pending_close_invoice_process) {
            try {
                if(Yii::$app->mutex->acquire('mutex_close_bills')) {
                    Yii::$app->cache->set('_invoice_close_errors' ,[]);
                    $this->closePendingBills($pending_close_invoice_process);
                }
            } catch (\Exception $ex) {
                echo "ERROR__________". $ex->getMessage() ."\n" . $ex->getTraceAsString();
                \Yii::info('ERROR ________________ ' . $ex->getMessage() ."\n" .$ex->getTraceAsString(), 'facturacion-cerrado');
            }
        }
    }

    /**
     * Crea todos los comprobantes que se deben incluir en el proceso de facturacion indicado
     */
    public function invoiceAll()
    {
        $cti = new ContractToInvoicePrueba();
        $cti->invoiceAll();
    }

    /**
     * Cierra todos los comprobantes que se deben incluir en el proceso de facturacion indicado
     */
    public function closePendingBills($invoice_process)
    {
        $i = 0;
        $searchModel = new BillSearch();
        $query = $searchModel->searchPendingToClose([
            'BillSearch' => [
                'company_id' => $invoice_process->company_id,
                'bill_type_id' => $invoice_process->bill_type_id,
                'period' => $invoice_process->period,
            ]
        ]);

        $total = $query->count();

        Yii::$app->cache->set('_invoice_close_process_', [
            'total' => $total,
            'qty' => $i
        ]);

        $list_client = [];
        foreach ($query->batch() as $bills) {
            foreach ($bills as $bill) {
                /*Verificar si el proceso esta pausado*/
                $paused_close_invoice_process = InvoiceProcess::getPausedInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS);
                $pending_close_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CLOSE_BILLS);
                if(!empty($paused_close_invoice_process)){
                    if($paused_close_invoice_process->status == InvoiceProcess::STATUS_PAUSED){
                        echo "This close-process was paused.\n";
                        return null;
                    }
                }
                if(!in_array($bill->customer_id,$list_client)){
                    if(!empty($pending_close_invoice_process)){
                        $bill->verifyNumberAndDate();

                        if($bill->close()){
                            $i++;
                            Yii::$app->cache->set('_invoice_close_process_', [
                                'total' => $total,
                                'qty' => $i
                            ]);
                            $list_client[] = [$bill->customer_id,'cliente facturado correctamente.'];
                            echo $bill->customer_id . " cliente facturado correctamente. \n";
                        }else{
                            echo "The invoice could not be closed\n";
                            return null;  
                        }
                    }
                }else{
                    echo $bill->customer_id . " cliente duplicado. \n";
                    $list_client[] = [$bill->customer_id,'cliente duplicado'];
                    \Yii::info('---------------------------------------------------------------------------------------------'.
                        "ID Factura: " . $bill->bill_id . "\n" .
                        "Codigo cliente: " . $bill->customer_id . "\n" .
                        "Estado: " . $bill->status . "\n" . 
                        "Pagado: " . $bill->payed . "\n" .
                        "Número: " . $bill->number . "\n" . 
                        "Cliente duplicado. \n", 'duplicados-afip');
                }
            }
        }

        InvoiceProcess::endProcess(InvoiceProcess::TYPE_CLOSE_BILLS);
    }
}
