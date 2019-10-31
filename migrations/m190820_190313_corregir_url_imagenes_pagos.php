<?php

use yii\db\Migration;

/**
 * Class m190820_190313_corregir_url_imagenes_pagos
 */
class m190820_190313_corregir_url_imagenes_pagos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $notify_payments= \app\modules\westnet\models\NotifyPayment::find()->all();

        foreach ($notify_payments as $payment) {
            if($payment->image_receipt !== null) {
                $route= explode('/', $payment->image_receipt);
                $file = $route[count($route) - 1];
                $newroute= 'uploads/payments/'. $file;
                $payment->updateAttributes(['image_receipt' => $newroute]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_190313_corregir_url_imagenes_pagos cannot be reverted.\n";

        return false;
    }
    */
}
