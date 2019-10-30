<?php

use yii\db\Migration;

class m190802_165454_add_invoice_process_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('invoice_process',[
            'invoice_process_id' => $this->primaryKey(),
            'start_datetime' => $this->integer(),
            'end_datetime' => $this->integer()->defaultValue(null),
            'company_id' => $this->integer(),
            'bill_type_id' => $this->integer(),
            'period' => $this->string(),
            'status' => "ENUM('pending','error','finished')",
            'observation' => $this->text()->defaultValue(null),
            'type' => "ENUM('create_bills', 'close_bills')"
        ]);

        $this->addForeignKey('fk_invoice_process_company_id', 'invoice_process', 'company_id', 'company', 'company_id');
        $this->addForeignKey('fk_invoice_process_bill_type_id', 'invoice_process', 'bill_type_id', 'bill_type', 'bill_type_id');
    }

    public function safeDown()
    {
        $this->dropTable('invoice_process');
    }
}
