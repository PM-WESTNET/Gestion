<?php

use yii\db\Migration;

/**
 * Handles adding has_direct_debit to table `customer`.
 */
class m210623_132256_add_has_direct_debit_columns_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer}}','has_direct_debit', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}','has_direct_debit');
    }
}
