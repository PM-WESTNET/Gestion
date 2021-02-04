<?php

use yii\db\Migration;

/**
 * Class m190701_141120_message_column_in_infobip_table
 */
class m190701_141120_message_column_in_infobip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('infobip_message', 'message', 'TEXT NULL');
        $this->addColumn('infobip_message', 'customer_id', 'INTEGER(11) NULL');

        $this->addForeignKey('fk_infobip_message_customer', 'infobip_message', 'customer_id', 'customer', 'customer_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('infobip_message', 'message');
        $this->dropColumn('infobip_message', 'customer_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190701_141120_message_column_in_infobip_table cannot be reverted.\n";

        return false;
    }
    */
}
