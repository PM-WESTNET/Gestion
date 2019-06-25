<?php
use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\Role;

class m190408_100505_add_rol_see_agenda_in_home extends Migration
{
    public function safeUp()
    {
        if (Role::findOne(['name' => 'home_is_agenda']) === null){
            Role::create('home_is_agenda', 'La pantalla principal es la agenda');
        }
    }

    public function safeDown()
    {
        Role::deleteIfExists(['name' => 'home_is_agenda']);
    }
}