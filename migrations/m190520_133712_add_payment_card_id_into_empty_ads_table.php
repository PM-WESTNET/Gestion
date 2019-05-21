<?php

use yii\db\Migration;

class m190520_133712_add_payment_card_id_into_empty_ads_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('empty_ads', 'payment_card_id', $this->integer());

        $this->addForeignKey('fk_empty_ads_payment_card_id', 'empty_ads', 'payment_card_id', 'payment_card', 'payment_card_id');
    }

    public function safeDown()
    {
        $this->dropColumn('empty_ads', 'payment_card_id');
    }
}
