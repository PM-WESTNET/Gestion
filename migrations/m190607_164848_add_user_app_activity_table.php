<?php

use yii\db\Migration;

class m190607_164848_add_user_app_activity_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('user_app_activity', [
           'user_app_activity_id' => $this->primaryKey(),
           'user_app_id' => $this->integer(),
           'installation_datetime' => $this->integer(),
           'last_activity_datetime' => $this->integer()
        ]);

        $this->addForeignKey('fk_user_app_activity_user_app_id', 'user_app_activity', 'user_app_id', 'user_app', 'user_app_id');
    }

    public function safeDown()
    {
        $this->dropTable('user_app_activity');
    }
}
