<?php

use yii\db\Migration;

/**
 * Handles the creation of table `customer_previous_company`.
 */
class m220621_143143_create_customer_previous_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customer_previous_company', [
            'id' => $this->primaryKey(),
            'company' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customer_previous_company');
    }
}
