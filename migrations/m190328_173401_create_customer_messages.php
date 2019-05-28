<?php

use yii\db\Migration;

/**
 * Class m190328_173401_create_customer_messages
 */
class m190328_173401_create_customer_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('customer_message', [
            'name' => 'Código de pago',
            'status' => \app\modules\sale\models\CustomerMessage::STATUS_ENABLED,
            'message' => 'Estimado {customer_name} le enviamos su código de pago: {payment_code}. Atentamente Westnet.'
        ]);

        $this->insert('customer_message', [
            'name' => 'Monto de Pago',
            'status' => \app\modules\sale\models\CustomerMessage::STATUS_ENABLED,
            'message' => 'Estimado {customer_name} su monto actual a pagar es {debt}. Atentamente Westnet.'
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190328_173401_create_customer_messages cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190328_173401_create_customer_messages cannot be reverted.\n";

        return false;
    }
    */
}
