<?php

use yii\db\Migration;

class m190403_112418_add_type_and_text_into_app_failed_register_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('app_failed_register', 'type', "ENUM('register','contact')");

        $this->update('app_failed_register', ['type' => 'register']);

        $this->addColumn('app_failed_register', 'text', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('app_failed_register', 'text');
        $this->dropColumn('app_failed_register', 'type');
    }
}
