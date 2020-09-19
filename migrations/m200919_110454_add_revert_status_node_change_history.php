<?php

use yii\db\Migration;

/**
 * Class m200919_000454_add_revert_status_node_change_history
 */
class m200919_110454_add_revert_status_node_change_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('node_change_history', 'status', "ENUM('error', 'applied', 'reverted')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('node_change_history', 'status', "ENUM('error', 'applied')");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200919_000454_add_revert_status_node_change_history cannot be reverted.\n";

        return false;
    }
    */
}
