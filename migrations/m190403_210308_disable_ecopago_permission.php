<?php

use yii\db\Migration;

/**
 * Class m190403_210308_disable_ecopago_permission
 */
class m190403_210308_disable_ecopago_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$users = \webvimark\modules\UserManagement\models\User::findOne(['username', ['frios', 'frios1']]);
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission('ecooff', 'disable_ecopago', [
            'westnet/ecopagos/ecopago/disable'
        ], 'Can Disable Ecopago', 'Ecopagos');

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
        echo "m190403_210308_disable_ecopago_permission cannot be reverted.\n";

        return false;
    }
    */
}
