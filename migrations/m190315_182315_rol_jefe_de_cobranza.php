<?php

use yii\db\Migration;

/**
 * Class m190315_182315_rol_jefe_de_cobranza
 */
class m190315_182315_rol_jefe_de_cobranza extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (\webvimark\modules\UserManagement\models\rbacDB\Role::findOne(['name' => 'collection_manager']) === null){
            \webvimark\modules\UserManagement\models\rbacDB\Role::create('collection_manager', 'Jefe de Cobranza');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::deleteIfExists(['name' => 'collection_manager']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190315_182315_rol_jefe_de_cobranza cannot be reverted.\n";

        return false;
    }
    */
}
