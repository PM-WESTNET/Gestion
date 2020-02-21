<?php

use yii\db\Migration;

/**
 * Class m200219_150432_admin_payment_methods_permission
 */
class m200219_150432_admin_payment_methods_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('payment_method_admin', 'Administrador de Medios de Pagos', 'Clientes');
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'payment_method_admin',
            'admin_payment_method',
            [
                'checkout/payment-method/index',
                'checkout/payment-method/view',
                'checkout/payment-method/create',
                'checkout/payment-method/update',
                'checkout/payment-method/delete',
            ],
            'Administrar Medios de Pago',
            'Clientes'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200219_150432_admin_payment_methods_permission cannot be reverted.\n";

        return false;
    }
    */
}
