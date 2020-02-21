<?php

use yii\db\Migration;

/**
 * Class m200213_193707_employee_caegory_permissions
 */
class m200213_193707_employee_caegory_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'employee_admin',
            'employee_category_admin',
            [
                'employee/employee-category/index',
                'employee/employee-category/view',
                'employee/employee-category/create',
                'employee/employee-category/update',
                'employee/employee-category/delete',
            ],
            'Administrar Categorias de Empleado',
            'Contabilidad'
            );
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
        echo "m200213_193707_employee_caegory_permissions cannot be reverted.\n";

        return false;
    }
    */
}
