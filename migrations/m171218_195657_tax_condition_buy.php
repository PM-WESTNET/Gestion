<?php

use yii\db\Migration;

class m171218_195657_tax_condition_buy extends Migration
{
    public function up()
    {

        $this->execute( "create table tax_condition_has_bill_type_buy ( " .
                "tax_condition_id int not null, " .
                "bill_type_id int not null, " .
                "`order` int null, " .
                "primary key (tax_condition_id, bill_type_id), " .
                "constraint fk_tax_condition_has_bill_type_buy_tax_condition1 " .
                "foreign key (tax_condition_id) references tax_condition (tax_condition_id), " .
                "constraint fk_tax_condition_has_bill_type_buy_bill_type1 " .
                "foreign key (bill_type_id) references bill_type (bill_type_id)  ) " );

        $this->execute( "create index fk_tax_condition_has_bill_type_buy_bill_type1_idx " .
                        "on tax_condition_has_bill_type_buy (bill_type_id) ");

        $this->execute( "create index fk_tax_condition_has_bill_type_buy_tax_condition1_idx " .
                  "on tax_condition_has_bill_type_buy (tax_condition_id) ");
    }

    public function down()
    {
        echo "m171218_195657_tax_condition_buy cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
