<?php

use app\modules\checkout\models\PagoFacilTransmitionFile;
use app\modules\sale\models\Customer;
use webvimark\modules\UserManagement\models\rbacDB\Permission;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use yii\db\Migration;

class m191211_163030_add_role_and_permision_to_configure_company_billing extends Migration
{
    public function safeUp()
    {
        Role::create(
            'Configurador de facturacion',
            'Puede declarar la empresa por a la cual se asignan los clientes nuevos',
            'Clientes'
        );

        Permission::create(
            'Puede configurar facturacion de clientes nuevos',
            'Permite configurar la empresa de los clientes nuevos',
            'Clientes'
        );

        Permission::assignRoutes('Puede configurar facturacion de clientes nuevos', [
            '/sale/company-has-billing/*',
            '/sale/company-has-billing/index',
            '/sale/company-has-billing/save'
        ]);

        Role::assignRoutesViaPermission(
            'Configurador de facturacion',
            'Puede configurar facturacion de clientes nuevos',
            [
                '/sale/company-has-billing/*',
                '/sale/company-has-billing/index',
                '/sale/company-has-billing/save'
            ]
        );
    }

    public function safeDown()
    {
        return true;
    }
}
