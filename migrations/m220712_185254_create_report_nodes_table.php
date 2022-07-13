<?php

use yii\db\Migration;

/**
 * Handles the creation of table `report_nodes`.
 */
class m220712_185254_create_report_nodes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('report_nodes', [
            'report_nodes_id' => $this->primaryKey(),
            'report' => $this->string()->notNull(),
            'period' => $this->integer()->notNull(),
            'value' => $this->double()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('report_nodes');
    }
}
