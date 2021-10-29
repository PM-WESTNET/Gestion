<?php

use yii\db\Migration;

/**
 * Class m211029_185152_add_smartolt_olt_id_to_node_table
 */
class m211029_185152_add_smartolt_olt_id_to_node_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%node}}', 'smartolt_olt_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%node}}','smartolt_olt_id');
    }
}
