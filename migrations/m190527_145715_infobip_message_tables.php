<?php

use yii\db\Migration;

/**
 * Class m190527_145715_infobip_message_tables
 */
class m190527_145715_infobip_message_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('infobip_message', [
           'infobip_message_id' => $this->primaryKey(),
           'bulkId' => $this->string(255)->notNull(),
           'messageId' => $this->string(45)->notNull(),
           'to' => $this->string(45)->notNull(),
           'status' => $this->string(45)->notNull(),
           'status_description' => $this->string(255),
           'sent_timestamp' => $this->integer()->notNull()
        ]);


        $this->createTable('infobip_response', [
            'infobip_response_id' => $this->primaryKey(),
            'from' => $this->string(45)->notNull(),
            'to' => $this->string(45)->notNull(),
            'content' => $this->text(),
            'keyword' => $this->string(45),
            'received_timestamp' => $this->integer()->notNull()
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190527_145715_infobip_message_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190527_145715_infobip_message_tables cannot be reverted.\n";

        return false;
    }
    */
}
