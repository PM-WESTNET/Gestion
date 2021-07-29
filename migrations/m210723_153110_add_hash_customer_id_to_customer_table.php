<?php

use yii\db\Migration;

/**
 * Class m210723_153110_add_hash_customer_id_to_customer_table
 */
class m210723_153110_add_hash_customer_id_to_customer_table extends Migration
{
    public function safeUp()
    {   
        $this->addColumn('{{%customer}}', 'hash_customer_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}','hash_customer_id');
    }
}
