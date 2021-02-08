<?php

use yii\db\Migration;

/**
 * Class m190523_211802_installations_manager_roll
 */
class m190523_211802_installations_manager_roll extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (\webvimark\modules\UserManagement\models\rbacDB\Role::findOne(['name' => 'installations_manager']) === null){
            \webvimark\modules\UserManagement\models\rbacDB\Role::create('installations_manager', 'Jefe de Gestión de Instalaciones');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::deleteIfExists(['name' => 'installations_manager']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190523_211802_installations_manager_roll cannot be reverted.\n";

        return false;
    }
    */
}
