<?php

use yii\db\Migration;

/**
 * Class m190607_215505_app_register_failed_ticket_id_column
 */
class m190607_215505_app_register_failed_ticket_id_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('app_failed_register', 'ticket_id', 'INT(11) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('app_failed_register', 'ticket_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190607_215505_app_register_failed_ticket_id_column cannot be reverted.\n";

        return false;
    }
    */
}
