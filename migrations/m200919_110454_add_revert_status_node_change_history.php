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
        $this->addColumn('node_change_history', 'status', "ENUM('error', 'applied', 'reverted', 'pending')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('node_change_history', 'status');
    }

   
}
