<?php

use yii\db\Migration;
use app\modules\checkout\models\PaymentMethod;

class m190806_130101_add_notify_payment_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('notify_payment', [
            'notify_payment_id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'date' => $this->dateTime(),
            'amount' => $this->float(),
            'payment_method_id' => $this->integer(),
            'image_receipt' => $this->text(),
            'created_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_notify_payment_customer_id', 'notify_payment', 'customer_id', 'customer', 'customer_id');
        $this->addForeignKey('fk_notify_payment_payment_method_id', 'notify_payment', 'payment_method_id', 'payment_method', 'payment_method_id');

        $this->addColumn('payment_method', 'show_in_app', $this->boolean());

        foreach (PaymentMethod::find()->all() as $payment_method) {
            $payment_method->updateAttributes(['show_in_app' => false]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('payment_method', 'show_in_app');
        $this->dropColumn('payment_method', 'show_in_app');
        $this->dropTable('notify_payment');
    }
}
