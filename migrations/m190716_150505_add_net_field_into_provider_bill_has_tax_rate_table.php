<?php

use yii\db\Migration;

class m190716_150505_add_net_field_into_provider_bill_has_tax_rate_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('provider_bill_has_tax_rate', 'net', $this->double());
    }

    public function safeDown()
    {
        $this->dropColumn('provider_bill_has_tax_rate', 'net');
    }

}
