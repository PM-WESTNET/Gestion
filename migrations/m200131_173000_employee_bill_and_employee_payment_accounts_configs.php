<?php

use yii\db\Migration;

/**
 * Class m200131_173000_employee_bill_and_employee_payment_accounts_configs
 */
class m200131_173000_employee_bill_and_employee_payment_accounts_configs extends Migration
{

    private $configs = [
        [
            'name' => 'Factura Empleados',
            'class' => 'app\\modules\\employee\\models\\EmployeeBill',
            'classMovement' => 'app\\modules\\accounting\\components\\impl\\EmployeeBillMovement',

        ],
        [
            'name' => 'Pago Empleados',
            'class' => 'app\\modules\\employee\\models\\EmployeePayment',
            'classMovement' => 'app\\modules\\accounting\\components\\impl\\EmployeePaymentMovement',
        ],
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sueldosYJornalesAPagar = \app\modules\accounting\models\Account::findOne(['name' => 'SUELDOS Y JORNALES A PAGAR']);

        if (empty($sueldosYJornalesAPagar)) {
            $pasivo = \app\modules\accounting\models\Account::findOne(['name' => 'PASIVO']);

            $sueldosYJornalesAPagar = new \app\modules\accounting\models\Account([
                'name' => 'SUELDOS Y JORNALES A PAGAR',
                'parent_account_id' => $pasivo->account_id,
                'is_usable' => true,
            ]);

            if (!$sueldosYJornalesAPagar->save() ) {
                return false;
            }
        }

        $sueldosYJornales = \app\modules\accounting\models\Account::findOne(['name' => 'SUELDOS Y JORNALES']);

        $billAccountsConfig = new \app\modules\accounting\models\AccountConfig([
            'name' => 'Factura Empleados',
            'class' => 'app\\modules\\employee\\models\\EmployeeBill',
            'classMovement' => 'app\\modules\\accounting\\components\\impl\\EmployeeBillMovement',
        ]);

        if (!$billAccountsConfig->save()){
            return false;
        }

        $billAccountsConfig->addAccount([
            'account_id' => $sueldosYJornales->account_id,
            'account_config_id' => $billAccountsConfig->account_config_id,
            'attrib' => 'total',
            'is_debit' => true
        ]);

        $billAccountsConfig->addAccount([
            'account_id' => $sueldosYJornalesAPagar->account_id,
            'account_config_id' => $billAccountsConfig->account_config_id,
            'attrib' => 'total',
            'is_debit' => false
        ]);

        $paymentAccountsConfig = new \app\modules\accounting\models\AccountConfig([
            'name' => 'Pago Empleados',
            'class' => 'app\\modules\\employee\\models\\EmployeePayment',
            'classMovement' => 'app\\modules\\accounting\\components\\impl\\EmployeePaymentMovement',
        ]);

        if (!$paymentAccountsConfig->save()){
            return false;
        }

        $paymentAccountsConfig->addAccount([
            'account_id' => $sueldosYJornalesAPagar->account_id,
            'account_config_id' => $paymentAccountsConfig->account_config_id,
            'attrib' => 'total',
            'is_debit' => true
        ]);

        $cajaYBancos  = \app\modules\accounting\models\Account::findOne(['name' => 'CAJA Y BANCOS']);
        $contado = \app\modules\checkout\models\PaymentMethod::findOne(['name' => 'Contado']);

        $paymentAccountsConfig->addAccount([
            'account_id' => $cajaYBancos->account_id,
            'account_config_id' => $paymentAccountsConfig->account_config_id,
            'attrib' => $contado->payment_method_id,
            'is_debit' => false
        ]);

        $transferencia = \app\modules\checkout\models\PaymentMethod::findOne(['name' => 'Transferencia']);

        $paymentAccountsConfig->addAccount([
            'account_id' => $cajaYBancos->account_id,
            'account_config_id' => $paymentAccountsConfig->account_config_id,
            'attrib' => $transferencia->payment_method_id,
            'is_debit' => false
        ]);




    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $bills = \app\modules\accounting\models\AccountConfig::findOne(['name' => 'Factura Empleados']);

        if ($bills) {
            $bills->delete();
        }

        $payment = \app\modules\accounting\models\AccountConfig::findOne(['name' => 'Pago Empleados']);

        if ($payment){
            $payment->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200131_173000_employee_bill_and_employee_payment_accounts_configs cannot be reverted.\n";

        return false;
    }
    */
}
