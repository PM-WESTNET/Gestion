<?php

use yii\db\Migration;

/**
 * Handles adding vlan to table `node`.
 */
class m210809_164855_add_vlan_column_to_node_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%node}}','vlan', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%node}}','vlan');
    }
}
