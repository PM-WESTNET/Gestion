<?php

use yii\db\Migration;

class m190509_190504_add_bill_number_to_into_bill_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('bill', 'bill_number_to', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('bill', 'bill_number_to');
    }

}
