<?php

use yii\db\Migration;

class m160920_161859_notification_destinatary_debts extends Migration
{
    public function init() {
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function up()
    {
        $this->execute("ALTER TABLE destinatary ADD debt_from INT DEFAULT 0 NULL;");
        $this->execute("ALTER TABLE destinatary ADD debt_to INT DEFAULT 100000 NULL;");
    }

    public function down()
    {
        echo "m160920_161859_notification_destinatary_debts cannot be reverted.\n";

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
