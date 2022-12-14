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
use app\modules\alertsbot\controllers\TelegramController;

class InvoiceProcessController extends Controller
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
        $pending_invoice_process = InvoiceProcess::getPendingInvoiceProcess(InvoiceProcess::TYPE_CREATE_BILLS);

        if($pending_invoice_process) {
            try {
                if(Yii::$app->mutex->acquire('mutex_create_bills')) {
                    Yii::$app->cache->set('_invoice_create_errors', []);
                    $this->invoiceAll($pending_invoice_process);
                }
            } catch (\Exception $ex) {
                echo "ERROR__________". 'Linea '.$ex->getLine()."\n" .'Archivo '.$ex->getFile() ."\n" .$ex->getMessage() ."\n" .$ex->getTraceAsString()."\n";
                \Yii::info('ERROR ________________ ' . $ex->getMessage() ."\n" .$ex->getTraceAsString(), 'facturacion-creacion');
                // send error to telegram
                TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: invoice-process/control-creation-invoice-process ****', $ex);
            }
        }
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
                // send error to telegram
                TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: invoice-process/control-close-invoice-process ****', $ex);
            }
        }
    }

    /**
     * Crea todos los comprobantes que se deben incluir en el proceso de facturacion indicado
     */
    public function invoiceAll(InvoiceProcess $invoice_process)
    {
        $cti = new ContractToInvoice();

        echo 'company_id ' .$invoice_process->company_id ."\n" .'bill_type_id '.$invoice_process->bill_type_id . "\n".'period ' . $invoice_process->period . "\n" .'bill_observation '.$invoice_process->observation ."\n";

        //Verifico si la configuracion me indica que genere un archivo de pmc
        $generate_pmc_file = in_array($invoice_process->company_id, Yii::$app->params['companies_ids_to_generate_pmc_files_in_invoice_process']);

        if($generate_pmc_file){
            $invoice_process->createPagoMisCuentasFile();
        }

        $cti->invoiceAll(
            [
                'ContractSearch' => [
                    'company_id' => $invoice_process->company_id,
                    'bill_type_id' => $invoice_process->bill_type_id,
                    'period' => $invoice_process->period,
                ],
                'bill_observation' => $invoice_process->observation
            ],
            ($generate_pmc_file ? $invoice_process->invoice_process_id : null)
        );

        if($generate_pmc_file){
            $invoice_process->closePMCAssociatedFile();
        }
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
                            $bill->updateAttributes(['had_error' => true]);
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
                        "N??mero: " . $bill->number . "\n" . 
                        "Cliente duplicado. \n", 'duplicados-afip');
                }
            }
        }

        InvoiceProcess::endProcess(InvoiceProcess::TYPE_CLOSE_BILLS);
    }
}