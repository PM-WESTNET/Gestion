<?php

use yii\db\Migration;

/**
 * Class m181008_135536_billing_company
 */
class m181102_170813_change_type_to_column_number_from_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('payment', 'number', $this->integer(45));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('payment', 'number', $this->text(45));
    }
}
