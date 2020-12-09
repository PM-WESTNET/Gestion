<?php

use yii\db\Migration;

/**
 * Class m201029_132534_firstdata_payment_method
 */
class m201029_132534_firstdata_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('payment_method', [
            'name' => 'Firstdata',
            'status' => 'enabled',
            'register_number' => 0,
            'send_ivr' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

}
