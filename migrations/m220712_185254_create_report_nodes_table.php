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
            'id' => $this->primaryKey(),
            'report' => $this->varchar(55)->notNull()->unique(),
            'period' => $this->int(),
            'value' => $this->double(),
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
