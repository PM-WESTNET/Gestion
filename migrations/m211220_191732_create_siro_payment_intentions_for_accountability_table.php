<?php

use yii\db\Migration;

/**
 * Handles the creation of table `siro_payment_intentions_for_accountability`.
 */
class m211220_191732_create_siro_payment_intentions_for_accountability_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('payment_intentions_accountability', [
            'payment_intention_accountability_id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'siro_payment_intention_id' => $this->integer(),
            'total_amount' => $this->double(),
            'payment_method' => $this->string(),
            'status' => $this->string(),
            'collection_channel_description' => $this->string(),
            'collection_channel' => $this->string(),
            'rejection_code' => $this->string(),
            'payment_date' => $this->date(),
            'accreditation_date' => $this->date()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('payment_intentions_accountability');
    }
}
