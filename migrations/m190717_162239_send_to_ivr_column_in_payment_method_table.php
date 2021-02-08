<?php

use yii\db\Migration;

/**
 * Class m190717_162239_send_to_ivr_column_in_payment_method_table
 */
class m190717_162239_send_to_ivr_column_in_payment_method_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_method', 'send_ivr', 'BOOLEAN NOT NULL DEFAULT false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment_method', 'send_ivr');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190717_162239_send_to_ivr_column_in_payment_method_table cannot be reverted.\n";

        return false;
    }
    */
}
