<?php

use yii\db\Migration;

/**
 * Class m200921_155128_node_change_process_reverted_status
 */
class m200921_155128_node_change_process_reverted_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('node_change_process', 'status', "ENUM('created', 'pending', 'finished', 'reverted')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('node_change_process', 'status', "ENUM('created', 'pending', 'finished')");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200921_155128_node_change_process_reverted_status cannot be reverted.\n";

        return false;
    }
    */
}
