<?php

use yii\db\Migration;

class m190522_150808_add_customer_has_payment_track extends Migration
{
    public function safeUp()
    {
        $this->createTable('customer_has_payment_track', [
            'customer_has_payment_track' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'payment_method_id' => $this->integer(),
            'track_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_customer_has_payment_track_customer_id', 'customer_has_payment_track', 'customer_id', 'customer', 'customer_id');
        $this->addForeignKey('fk_customer_has_payment_track_payment_method_id', 'customer_has_payment_track', 'payment_method_id', 'payment_method', 'payment_method_id');
        $this->addForeignKey('fk_customer_has_payment_track_track_id', 'customer_has_payment_track', 'track_id', 'track', 'track_id');
    }

    public function safeDown()
    {
        $this->dropTable('customer_has_payment_track');
    }
}
