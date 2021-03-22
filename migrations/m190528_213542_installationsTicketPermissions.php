<?php

use yii\db\Migration;

/**
 * Class m190528_213542_installationsTicketPermissions
 */
class m190528_213542_installationsTicketPermissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission('installations_manager', 'can-admin-installations', [
            '/ticket/ticket/installations-tickets',
            '/ticket/ticket/create-and-assign-user',
            '/ticket/ticket/customers-has-category-ticket'
        ], 'Can manage Installations Tickets', 'Tickets');

        \webvimark\modules\UserManagement\models\rbacDB\Permission::assignRoutes('view-installations-tickets', [
            '/ticket/ticket/installations-tickets',
        ], 'Can view Installations Ticket', 'Tickets');
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
        echo "m190528_213542_installationsTicketPermissions cannot be reverted.\n";

        return false;
    }
    */
}
