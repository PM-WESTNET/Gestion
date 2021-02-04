<?php

use yii\db\Migration;

class m160811_193003_ecopago_provider extends Migration
{

    public function init() {
        $this->db = 'dbecopago';
        parent::init();
    }

    public function up()
    {
        $this->execute("ALTER TABLE ecopago ADD COLUMN provider_id INT(11) NOT NULL AFTER number");
        $this->execute("ALTER TABLE ecopago ADD INDEX fk_ecopago_provider_id (provider_id ASC)");
    }

    public function down()
    {
        echo "m160811_193003_ecopago_provider cannot be reverted.\n";

        return false;
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
