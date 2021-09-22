<?php

use yii\db\Migration;

/**
 * Handles adding created_by to table `user`.
 */
class m210921_154020_add_created_by_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'created_by', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}','vlan');
    }
}
