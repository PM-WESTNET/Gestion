<?php

use yii\db\Migration;

/**
 * Class m200423_200350_add_invoice_process_id_into_bill_table
 */
class m200423_200350_add_invoice_process_id_into_bill_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bill', 'invoice_process_id', $this->integer()->defaultValue(null));

        $this->addForeignKey('fk_bill_invoice_process_id', 'bill', 'invoice_process_id', 'invoice_process', 'invoice_process_id');

        $this->addColumn('pagomiscuentas_file', 'created_by_invoice_process_id', $this->integer()->defaultValue(null));

        $this->addForeignKey('fk_pagomiscuentas_file_created_by_invoice_process_id', 'pagomiscuentas_file', 'created_by_invoice_process_id', 'invoice_process', 'invoice_process_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('bill', 'invoice_process_id');
    }
}
