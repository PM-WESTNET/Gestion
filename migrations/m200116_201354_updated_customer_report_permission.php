<?php

use yii\db\Migration;

/**
 * Class m200116_201354_updated_customer_report_permission
 */
class m200116_201354_updated_customer_report_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('can_view_updated_customer_report', 'Can view Updated Customer Report');
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'can_view_updated_customer_report',
            'updated_customer_report',
            [
                'reports/customer/customers-updated'
            ],
            'Updated Customer Report');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Permission::deleteIfExists(['name' => 'updated_customer_report']);
        \webvimark\modules\UserManagement\models\rbacDB\Role::deleteIfExists(['name' => 'can_view_updated_customer_report']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200116_201354_updated_customer_report_permission cannot be reverted.\n";

        return false;
    }
    */
}
