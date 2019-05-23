<?php

use yii\db\Migration;
use app\modules\checkout\models\PaymentMethod;

class m190523_122828_add_allow_config_field_into_payment_method_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payment_method', 'allow_track_config', $this->boolean());
        PaymentMethod::updateAll(['allow_track_config' => 0]);

        $payment_method = PaymentMethod::find()->where(['name' => 'Pago Facil'])->one();
        if($payment_method) {
            $payment_method->updateAttributes(['allow_track_config' => 1]);
        }

        $payment_method = PaymentMethod::find()->where(['name' => 'Pagomiscuentas'])->one();
        if($payment_method) {
            $payment_method->updateAttributes(['allow_track_config' => 1]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('payment_method', 'allow_track_config');
    }
}
