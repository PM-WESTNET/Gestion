<?php

use yii\db\Migration;

/**
 * Class m191111_192951_update_contract_manual_role
 */
class m191111_192951_update_contract_manual_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('controlador-isp', 'Controlador ISP');
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'controlador-isp',
            'update-contract-manually-isp',
            [
                'sale/contract/contract/update-on-isp'
            ],
            'Actualizar Contrato manualmente en ISP');
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
        echo "m191111_192951_update_contract_manual_role cannot be reverted.\n";

        return false;
    }
    */
}
