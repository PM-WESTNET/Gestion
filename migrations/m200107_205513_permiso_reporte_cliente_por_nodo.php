<?php

use yii\db\Migration;

/**
 * Class m200107_205513_permiso_reporte_cliente_por_nodo
 */
class m200107_205513_permiso_reporte_cliente_por_nodo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('customer_by_node_report', 'Reporte Clientes por nodo');

        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'customer_by_node_report', 'view_customer_by_node',[
                'reports/reports/customers-by-node'
            ],'Ver Reporte Clientes por nodo');
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
        echo "m200107_205513_permiso_reporte_cliente_por_nodo cannot be reverted.\n";

        return false;
    }
    */
}
