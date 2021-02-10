<?php

use yii\db\Migration;

/**
 * Class m201210_140036_access_point_table
 */
class m201210_140036_access_point_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('access_point', [
            'access_point_id' => $this->primaryKey()->notNull(),
            'name' => $this->string(90)->notNull(),
            'status' => 'enum("enabled", "disabled") NOT NULL',
            'strategy_class' => $this->string(255)->notNull(),
            'node_id' => $this->integer(11)->notNull()
        ]);

        $this->addForeignKey('fk_access_point_node', 'access_point', 'node_id', 'node', 'node_id');

        $this->addColumn('ip_range', 'access_point_id', 'INT NULL');

        $this->addForeignKey('fk_ip_range_access_point', 'ip_range', 'access_point_id', 'access_point', 'access_point_id');

        $this->createTable('ip_address', [
            'ip_address_id' => $this->primaryKey()->notNull(),
            'ip_address' => $this->integer(11)->notNull(),
            'status' => 'enum("available", "assigned") NOT NULL',
            'ip_range_id' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_ip_address_range', 'ip_address', 'ip_range_id', 'ip_range', 'ip_range_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('ip_address');
        $this->dropColumn('ip_range', 'access_point_id');
        $this->dropTable('access_point');
    }

    
}
