<?php

use yii\db\Migration;

/**
 * Class m190625_192229_automatic_debit_type_customer_column
 */
class m190625_192229_automatic_debit_type_customer_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('automatic_debit', 'customer_type', 'ENUM("own", "other") NULL DEFAULT "own"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('automatic_debit', 'customer_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_192229_automatic_debit_type_customer_column cannot be reverted.\n";

        return false;
    }
    */
}
