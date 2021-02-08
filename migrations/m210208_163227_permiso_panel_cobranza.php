<?php

use webvimark\modules\UserManagement\models\rbacDB\Permission;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use yii\db\Migration;

/**
 * Class m210208_163227_permiso_panel_cobranza
 */
class m210208_163227_permiso_panel_cobranza extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Role::create( 'cashing_panel', 'Cashing panel' );
        Role::assignRoutesViaPermission(
            'cashing_panel'
            ,'view_cashing_panel'
            ,[ 'sale/customer/cashing-panel']
            ,'Come view to Cashing panel');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //echo "m210208_163227_permiso_panel_cobranza cannot be reverted.\n";
        Permission::deleteIfExists('view_cashing_panel');
        Role::deleteIfExists( 'cashing_panel');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_163227_permiso_panel_cobranza cannot be reverted.\n";

        return false;
    }
    */
}
