<?php

use yii\db\Migration;

/**
 * Handles adding total_bills to table `customer`.
 */
class m211116_121941_add_total_bills_column_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer}}','total_bills', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}','total_bills');
    }
}
