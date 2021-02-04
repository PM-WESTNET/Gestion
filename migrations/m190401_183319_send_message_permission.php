<?php

use yii\db\Migration;

/**
 * Class m190401_183319_send_message_permission
 */
class m190401_183319_send_message_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $roles = \webvimark\modules\UserManagement\models\rbacDB\Role::find()->all();

        foreach ($roles as $role) {
            \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission($role->name, 'can-send-message', ['/sale/customer/send-message'], 'Can Send message to Customer');
        }
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
        echo "m190401_183319_send_message_permission cannot be reverted.\n";

        return false;
    }
    */
}
