<?php

use yii\db\Migration;

/**
 * Class m200921_164121_change_node_process_permissions
 */
class m200921_164121_change_node_process_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('massive_change_node', 'Cambio Masivo de Nodos', 'Nodos');
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'massive_change_node',
            'can_change_node_massive',
            [
                'westnet/node-change-process/index',
                'westnet/node-change-process/view',
                'westnet/node-change-process/create',
                'westnet/node-change-process/process-file',
                'westnet/node-change-process/rollback-all',
                'westnet/node-change-process/rollback-history',
                'westnet/node-change-process/generate-result-csv',
            ],
            'Puede cambiar nodos masivamente',
            'Nodos'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }
    
}
