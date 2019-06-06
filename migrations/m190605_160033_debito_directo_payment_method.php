<?php

use yii\db\Migration;

/**
 * Class m190605_160033_debito_directo_payment_method
 */
class m190605_160033_debito_directo_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('payment_method', [
            'name' => 'Débito Directo',
            'status' => 'enabled',
            'register_number' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $method = \app\modules\checkout\models\PaymentMethod::findOne(['name' => 'Débito Directo' ]);

        if ($method) {
            $method->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190605_160033_debito_directo_payment_method cannot be reverted.\n";

        return false;
    }
    */
}
