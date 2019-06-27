<?php

use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\Permission;

/**
 * Class m190528_213542_installationsTicketPermissions
 */
class m190531_131111_infobip_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Permission::assignRoutes('can-send-customer-messages', [
            '/sale/customer/send-message',
        ], 'Can send customer message', 'Clientes');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
