<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 17/07/19
 * Time: 17:54
 */
namespace app\commands;

use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use yii\console\Controller;
use app\modules\checkout\models\search\PaymentSearch;
use Yii;
use app\modules\alertsbot\controllers\TelegramController;
use DateTime;

class CurrentAccountBalanceController extends Controller
{
    /**
     * Actualiza los saldos de los clientes con conexiones activas.
     * Busca los clientes que tienen contrato, conexion activa, y su ultima actualizacion de saldo sea distinta a hoy
     * Si el cliente tiene pagos o comprobantes nuevos, o su fecha de ultima actualizacion es nula, se hace el calculo de saldo completo y se actualizan los datos en la tabla del cliente
     */
    public function actionUpdateCurrentAccountBalance()
    {
        if(Yii::$app->mutex->acquire('mutex_update_current_account_balance')) {
            try {
                $now = DateTime::createFromFormat('U.u', microtime(true));
                echo "\nINICIO: ". $now->format('Y-m-d h:i:s.u'). "\n";

                $today = time();
                $customers_to_update = Customer::find()
                    ->leftJoin('contract con', 'con.customer_id = customer.customer_id')
                    ->leftJoin('connection conn', 'conn.contract_id = con.contract_id')
                    ->where(['con.status' => 'active'])
                    ->andWhere(['or',['<','customer.last_balance', $today], ['customer.last_balance' => null]])
                    ->all();

                echo "Cantidad de clientes a actualizar (activos/habilitados y forzados): ". count($customers_to_update) ."\n";
                

                foreach ($customers_to_update as $customer) {
                    $searchModel = new PaymentSearch();
                    $searchModel->customer_id = $customer->customer_id;
                    
                    // $total = $searchModel->totalCalculationForQuery($customer->customer_id);
                    $total = $searchModel->accountTotal();


                    // echo "Customer_ID: " . $customer->customer_id . " - " . "Update Total: " . round($total,2) . "\n";

                    $customer->updateAttributes(['current_account_balance' => round($total,2), 'last_balance' => $today]);
                }
                
                
                \Yii::$app->mutex->release('mutex_update_current_account_balance');
                $then = DateTime::createFromFormat('U.u', microtime(true));
                echo "FIN: ". $then->format('Y-m-d h:i:s:u'). "\n\n";

            } catch (\Exception $ex) {

                echo "Ha ocurrido un error en el proceso de actualizaci??n de saldos"."\n";
                $formatted_text = "\n";
                $formatted_text .= 'At - '.(new \DateTime())->format('d-m-Y H:i:s').' (server time) '."\n";
                $formatted_text .= 'File - '.$ex->getFile(). "\n";
                $formatted_text .= 'Error - '.$ex->getMessage()."\n";
                $formatted_text .= 'Line - '.$ex->getLine()."\n";
                $formatted_text .= 'Trace - '.$ex->getTraceAsString()."\n";
                
                echo $formatted_text;
                // send error to telegram
                TelegramController::sendProcessCrashMessage('**** Cronjob Error Catch: current-account-balance/update-current-account-balance ****', $ex);
            }
        }else{
            //echo "Ya hay un proceso corriendo \n";
        }
    }
}