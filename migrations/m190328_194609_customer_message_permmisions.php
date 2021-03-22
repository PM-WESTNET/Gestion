<?php

use yii\db\Migration;

/**
 * Class m190328_194609_customer_message_permmisions
 */
class m190328_194609_customer_message_permmisions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (\webvimark\modules\UserManagement\models\rbacDB\Role::findOne(['name' => 'collection_manager']) === null){
            \webvimark\modules\UserManagement\models\rbacDB\Role::create('collection_manager', 'Jefe de Cobranza');
        }

        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'collection_manager',
            'create-customer-message',
            [
                '/sale/customer-message/index',
                '/sale/customer-message/view',
                '/sale/customer-message/create',
                '/sale/customer-message/update',
                '/sale/customer-message/delete',
            ],
            'Can Create Customer Message'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190328_194609_customer_message_permmisions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190328_194609_customer_message_permmisions cannot be reverted.\n";

        return false;
    }
    */
}
