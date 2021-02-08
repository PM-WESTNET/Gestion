<?php

use yii\db\Migration;

class m190403_174343_add_customer_code_into_app_failed_register_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('app_failed_register', 'customer_code', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('app_failed_register', 'customer_code');
    }
}
