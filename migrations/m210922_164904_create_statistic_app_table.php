<?php

use yii\db\Migration;

/**
 * Handles the creation of table `statistic_app`.
 */
class m210922_164904_create_statistic_app_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('statistic_app', [
            'statistic_app_id' => $this->primaryKey(),
            'type' => $this->string(),
            'description' => $this->text(),
            'created_at' => $this->date()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('statistic_app');
    }
}
