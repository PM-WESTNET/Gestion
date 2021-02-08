<?php

use yii\db\Migration;

/**
 * Class m201125_150519_has_automatic_debit_column_customer
 */
class m201125_150519_has_automatic_debit_column_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'has_debit_automatic', 'ENUM("yes", "no") NULL DEFAULT "no"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer', 'has_debit_automatic');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_150519_has_automatic_debit_column_customer cannot be reverted.\n";

        return false;
    }
    */
}
