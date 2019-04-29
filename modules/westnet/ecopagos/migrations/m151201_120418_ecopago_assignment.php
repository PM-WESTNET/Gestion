<?php

use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\rbacDB\Permission;
use webvimark\modules\UserManagement\models\rbacDB\Route;
use yii\db\Migration;

class m151201_120418_ecopago_assignment extends Migration {

    public function init() {
        $this->db = 'dbecopago';
        parent::init();
    }

    public function safeUp() {

        Role::create('Cashier');
        Role::assignRoutesViaPermission('Cashier', 'westnetEcopagoFrontend', [
            '/westnet/ecopagos/frontend/*',
        ]);
        Permission::create('westnetEcopagoFrontend', 'Ecopago Frontend');
        Permission::assignRoutes('westnetEcopagoFrontend', [
            '/westnet/ecopagos/frontend/*',
        ]);
    }

    public function safeDown() {

        echo "m151201_120418_ecopago_assignment cannot be reverted.\n";
        return false;
    }

}
