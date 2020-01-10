<?php

use app\modules\westnet\models\NotifyPayment;
use yii\db\Migration;

class m200110_115555_correct_notify_payment_from_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $notify_payment = NotifyPayment::find()->where(['from' => 'IVR'])->all();

        foreach ($notify_payment as $payment) {
            if($payment->from == 'IVR') {
                $payment->updateAttributes(['from' => NotifyPayment::FROM_IVR]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
