<?php

use yii\db\Migration;

/**
 * Class m200922_194501_old_server_id_node_change_history
 */
class m200922_194501_old_server_id_node_change_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('node_change_history', 'old_server_id', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('node_change_history', 'old_server_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200922_194501_old_server_id_node_change_history cannot be reverted.\n";

        return false;
    }
    */
}
