<?php

use yii\db\Migration;

/**
 * Class m191218_181715_modify_account_movement_permmision
 */
class m191218_181715_modify_account_movement_permmision extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('modify-account-movement', 'Modificar Movimientos de Cuenta');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::deleteIfExists(['name' => 'modify-account-movement']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_181715_modify_account_movement_permmision cannot be reverted.\n";

        return false;
    }
    */
}
