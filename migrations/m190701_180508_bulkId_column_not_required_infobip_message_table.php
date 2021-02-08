<?php

use yii\db\Migration;

/**
 * Class m190701_180508_bulkId_column_not_required_infobip_message_table
 */
class m190701_180508_bulkId_column_not_required_infobip_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('infobip_message', 'bulkId', 'VARCHAR(255) NULL' );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('infobip_message', 'bulkId', 'VARCHAR(255) NOT NULL' );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190701_180508_bulkId_column_not_required_infobip_message_table cannot be reverted.\n";

        return false;
    }
    */
}
