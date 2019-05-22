<?php

use yii\db\Migration;

class m190520_133712_add_payment_codes_19_and_29_digits_into_customer extends Migration
{
    public function safeUp()
    {
        $this->addColumn('customer', 'payment_code_19_digits', $this->string());
        $this->addColumn('customer', 'payment_code_29_digits', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'payment_code_29_digits');
        $this->dropColumn('customer', 'payment_code_19_digits');
    }
}
