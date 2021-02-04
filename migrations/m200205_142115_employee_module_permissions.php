<?php

use yii\db\Migration;

/**
 * Class m200205_142115_employee_module_permissions
 */
class m200205_142115_employee_module_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('employee_admin', 'Administrar Empleados');

        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'employee_admin',
            'can_admin_employee',
            [
                'employee/*'
            ],
            'Puede Administrar los Empleados',
            'Contabilidad'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Permission::deleteIfExists(['name' => 'can_admin_employee']);
        \webvimark\modules\UserManagement\models\rbacDB\Role::deleteIfExists(['name' => 'employee_admin']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_142115_employee_module_permissions cannot be reverted.\n";

        return false;
    }
    */
}
