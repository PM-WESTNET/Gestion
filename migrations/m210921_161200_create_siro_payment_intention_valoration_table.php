<?php

use yii\db\Migration;

/**
 * Handles the creation of table `siro_payment_intention_valoration`.
 */
class m210921_161200_create_siro_payment_intention_valoration_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('siro_payment_intention_valoration', [
            'siro_payment_intention_valoration_id' => $this->primaryKey(),
            'name' => $this->string(),
            'email' => $this->string(),
            'description' => $this->text(),
            'siro_payment_intention_id' => $this->integer(),
            'created_at' => $this->date()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('siro_payment_intention_valoration');
    }
}
