<?php

use yii\db\Migration;

/**
 * Class m200909_201514_notification_error_message
 */
class m200919_100707_add_tables_to_change_node_history extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('node_change_process', [
            'node_change_process_id' => $this->primaryKey(),
            'created_at' => $this->dateTime(),
            'ended_at' => $this->dateTime(),
            'status' => "ENUM('created', 'pending', 'finished')",
            'node_id' => $this->integer(),
            'creator_user_id' => $this->integer(),
            'input_file' => $this->string(),
            'output_file' => $this->string()
        ]);

        $this->addForeignKey('fk_node_change_process_node_id', 'node_change_process', 'node_id', 'node', 'node_id');
        $this->addForeignKey('fk_node_change_process_creator_user_id', 'node_change_process', 'creator_user_id', 'user', 'id');

        $this->createTable('node_change_history', [
            'node_change_history_id' => $this->primaryKey(),
            'node_change_process_id' => $this->integer(),
            'old_node_id' => $this->integer(),
            'connection_id' => $this->integer(),
            'old_ip' => $this->integer(),
            'new_ip' => $this->integer(),
            'created_at' => $this->dateTime(),
            'status' => "ENUM('error', 'applied')"
        ]);

        $this->addForeignKey('fk_node_change_history_process_id', 'node_change_history', 'node_change_process_id', 'node_change_process', 'node_change_process_id');
        $this->addForeignKey('fk_node_change_history_old_node_id', 'node_change_history', 'old_node_id', 'node', 'node_id');
        $this->addForeignKey('fk_node_change_history_connection_id', 'node_change_history', 'connection_id', 'connection', 'connection_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->dropForeignKey('fk_node_change_history_connection_id', 'node_change_history');
        $this->dropForeignKey('fk_node_change_history_old_node_id', 'node_change_history');
        $this->dropForeignKey('fk_node_change_history_process_id', 'node_change_history');

        $this->dropTable('node_change_history');

        $this->dropForeignKey('fk_node_change_process_creator_user_id', 'node_change_process');
        $this->dropForeignKey('fk_node_change_process_node_id', 'node_change_process');

        $this->dropTable('node_change_process');
    }
}
