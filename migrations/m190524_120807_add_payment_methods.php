<?php

use yii\db\Migration;
use app\modules\checkout\models\PaymentMethod;

class m190524_120807_add_payment_methods extends Migration
{

    public function safeUp()
    {
        $this->addColumn('payment_method', 'type_code_if_isnt_direct_channel', "ENUM('code_19_digits', 'code_29_digits', 'none')");

        $this->insert('payment_method', [
            'name' => 'Rapipago',
            'status' => 'enabled',
            'register_number' => 0,
            'type' => 'exchanging',
            'allow_track_config' => 1,
            'type_code_if_isnt_direct_channel' => 'code_29_digits'
        ]);

        $this->insert('payment_method', [
            'name' => 'Red Link',
            'status' => 'enabled',
            'register_number' => 0,
            'type' => 'exchanging',
            'allow_track_config' => 1,
            'type_code_if_isnt_direct_channel' => 'code_19_digits'
        ]);

        $payment_method = PaymentMethod::find()->where(['name' => 'Pago Facil'])->one();
        if($payment_method) {
            $payment_method->updateAttributes(['type_code_if_isnt_direct_channel' => 'code_29_digits']);
        }
        $payment_method = PaymentMethod::find()->where(['name' => 'Pagomiscuentas'])->one();
        if($payment_method) {
            $payment_method->updateAttributes(['type_code_if_isnt_direct_channel' => 'code_19_digits']);
        }
    }


    public function safeDown()
    {
        $payment_method = PaymentMethod::find()->where(['name' => 'Red Link'])->one();
        $payment_method->delete();
        $payment_method = PaymentMethod::find()->where(['name' => 'Rapipago'])->one();
        $payment_method->delete();

        $this->dropColumn('payment_method', 'type_code_if_isnt_direct_channel');
    }
}
