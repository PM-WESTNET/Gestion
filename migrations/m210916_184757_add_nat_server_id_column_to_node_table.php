<?php

use yii\db\Migration;

/**
 * Handles adding nat_server_id to table `node`.
 */
class m210916_184757_add_nat_server_id_column_to_node_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%node}}','nat_server_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%node}}','nat_server_id');
    }
}
