<?php

use yii\db\Migration;

/**
 * Handles the creation of table `nat_server`.
 */
class m210916_183039_create_nat_server_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('nat_server', [
            'nat_server_id' => $this->primaryKey(),
            'description' => $this->string(),
            'status' => $this->boolean(),
            'created_at' => $this->date(),
            'updated_at' => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('nat_server');
    }
}
