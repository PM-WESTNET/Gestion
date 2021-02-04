<?php

use yii\db\Migration;

class m190717_170808_add_current_account_balance_field_into_customer_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('customer', 'current_account_balance', $this->double());
        $this->addColumn('customer', 'last_calculation_current_account_balance', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'last_calculation_current_account_balance');
        $this->dropColumn('customer', 'current_account_balance');
    }
}
