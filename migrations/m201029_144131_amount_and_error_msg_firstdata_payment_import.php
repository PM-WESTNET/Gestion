<?php

use yii\db\Migration;

/**
 * Class m201029_144131_amount_and_error_msg_firstdata_payment_import
 */
class m201029_144131_amount_and_error_msg_firstdata_payment_import extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_import_payment', 'amount', 'DOUBLE NULL');
        $this->addColumn('firstdata_import_payment', 'error', 'VARCHAR(255) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_import_payment', 'amount');
        $this->dropColumn('firstdata_import_payment', 'error');
    }

}
