<?php

use webvimark\modules\UserManagement\models\rbacDB\Role;
use yii\db\Migration;

/**
 * Class m210119_160456_customer_updated_by_user_report_permission
 */
class m210119_160456_customer_updated_by_user_report_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Role::assignRoutesViaPermission('can_view_updated_customer_report', 'customers_updated_by_user_report', [
            'reports/customer/customers-updated-by-user',
        ], 'Can view Customers Updated by User');
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
        echo "m210119_160456_customer_updated_by_user_report_permission cannot be reverted.\n";

        return false;
    }
    */
}
