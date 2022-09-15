<?php

use yii\db\Migration;

/**
 * Handles adding missing to table `payment_intention_accountability`.
 */
class m220915_161027_add_missing_columns_to_payment_intention_accountability_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // adding missing columns from previous programmers work to have more data availible
        $this->addColumn('payment_intentions_accountability', 'first_expiration', $this->date()->after('accreditation_date'));
        $this->addColumn('payment_intentions_accountability', 'bar_code', $this->string());
        $this->addColumn('payment_intentions_accountability', 'rejection_description', $this->string()->after('rejection_code'));
        $this->addColumn('payment_intentions_accountability', 'payment_quotas', $this->string());
        $this->addColumn('payment_intentions_accountability', 'card', $this->string());
        $this->addColumn('payment_intentions_accountability', 'filler', $this->string());
        $this->addColumn('payment_intentions_accountability', 'result_id', $this->string());

        //also alter date stamps to datetime types to have as timestamps
        $this->alterColumn('payment_intentions_accountability', 'created_at', $this->dateTime());
        $this->alterColumn('payment_intentions_accountability', 'updated_at', $this->dateTime());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment_intentions_accountability', 'first_expiration');
        $this->dropColumn('payment_intentions_accountability', 'bar_code');
        $this->dropColumn('payment_intentions_accountability', 'rejection_description');
        $this->dropColumn('payment_intentions_accountability', 'payment_quotas');
        $this->dropColumn('payment_intentions_accountability', 'card');
        $this->dropColumn('payment_intentions_accountability', 'filler');
        $this->dropColumn('payment_intentions_accountability', 'result_id');

        $this->alterColumn('payment_intentions_accountability', 'created_at', $this->date());
        $this->alterColumn('payment_intentions_accountability', 'updated_at', $this->date());
    }
}
