<?php

use yii\db\Migration;

class m190705_141919_create_debit_direct_failed_payment extends Migration
{
    public function safeUp()
    {
        $this->createTable('debit_direct_failed_payment', [
            'debit_direct_failed_payment_id' => $this->primaryKey(),
            'customer_code' => $this->string(),
            'amount' => $this->double(),
            'date' =>  $this->string(),
            'cbu' => $this->string(),
            'import_id' => $this->integer(),
            'error' => $this->text(),
        ]);

        $this->addForeignKey('debit_direct_failed_payment_import_id', 'debit_direct_failed_payment', 'import_id', 'debit_direct_import', 'debit_direct_import_id');
    }

    public function safeDown()
    {
       $this->dropTable('debit_credit_failed_payment');
    }
}
