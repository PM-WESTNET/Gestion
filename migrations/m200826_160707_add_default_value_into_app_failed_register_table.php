<?php

use yii\db\Migration;

/**
 * Class m200826_160707_add_default_value_into_app_failed_register_table
 */
class m200826_160707_add_default_value_into_app_failed_register_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('app_failed_register', 'document_type', $this->string()->defaultValue(null));
        $this->alterColumn('app_failed_register', 'document_number', $this->string()->defaultValue(null));
        $this->alterColumn('app_failed_register', 'email', $this->string()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('app_failed_register', 'email', $this->string());
        $this->alterColumn('app_failed_register', 'document_type', $this->string());
        $this->alterColumn('app_failed_register', 'document_number', $this->string());

    }
}
