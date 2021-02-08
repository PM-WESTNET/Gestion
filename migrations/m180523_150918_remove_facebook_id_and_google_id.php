<?php

use yii\db\Migration;

class m180523_150918_remove_facebook_id_and_google_id extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('user_app', 'facebook_id');
        $this->dropColumn('user_app', 'google_id');
    }

    public function safeDown()
    {
        $this->addColumn('user_app', 'facebook_id', 'VARCHAR(255) NULL');
        $this->addColumn('user_app', 'google_id', 'VARCHAR(255) NULL');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
