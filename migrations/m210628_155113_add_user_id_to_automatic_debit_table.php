<?php

use yii\db\Migration;

/**
 * Class m210628_155113_add_user_id_to_automatic_debit_table
 */
class m210628_155113_add_user_id_to_automatic_debit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%automatic_debit}}','user_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%automatic_debit}}','user_id');
    }
}
