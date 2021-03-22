<?php

use webvimark\modules\UserManagement\models\rbacDB\Permission;
use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\Role;

/**
 * Class m210107_174909_ap_admin_permission
 */
class m210107_174909_ap_admin_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Role::create('access-point-admin', 'Admin Access Point');

        Role::assignRoutesViaPermission('access-point-admin', 'access-point', [
            'westnet/access-point/index',
            'westnet/access-point/create',
            'westnet/access-point/view',
            'westnet/access-point/update',
            'westnet/access-point/delete',
            'westnet/access-point/asign-ip-range',
            'westnet/ip-range/index',
            'westnet/ip-range/create',
            'westnet/ip-range/view',
            'westnet/ip-range/update',
            'westnet/ip-range/delete',
        ], 'Can admin Access Point');

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
        echo "m210107_174909_ap_admin_permission cannot be reverted.\n";

        return false;
    }
    */
}
