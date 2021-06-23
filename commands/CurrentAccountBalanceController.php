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

class CurrentAccountBalanceController extends Controller
{
    /**
     * Actualiza los saldos de los clientes con conexiones activas.
     * Busca los clientes que tienen contrato, conexion activa, y su ultima actualizacion de saldo sea distinta a hoy
     * Si el cliente tiene pagos o comprobantes nuevos, o su fecha de ultima actualizacion es nula, se hace el calculo de saldo completo y se actualizan los datos en la tabla del cliente
     */
    public function actionUpdateCurrentAccountBalance()
    {
        $today = time();

        $customers_to_update = Customer::find()
            ->leftJoin('contract con', 'con.customer_id = customer.customer_id')
            ->leftJoin('connection conn', 'conn.contract_id = con.contract_id')
            ->where(['con.status' => 'active'])
            ->where(['in','conn.status', ['enabled', 'forced']])
            ->andWhere(['or',['<','customer.last_balance', $today], ['customer.last_balance' => null]])
            ->all();

        echo "Clientes con conexiones activas que no han sido actualizados hoy: ". count($customers_to_update) ."\n";

        foreach ($customers_to_update as $customer) {
            $searchModel = new PaymentSearch();
            $searchModel->customer_id = $customer->customer_id;
            
            $total = $searchModel->totalCalculationForQuery($customer->customer_id);

            echo "Customer_ID: " . $customer->customer_id . "\n" . "Update Total: " . round($total,2) . "\n";

            $customer->updateAttributes(['current_account_balance' => round($total,2), 'last_balance' => $today]);
        }
    }
}