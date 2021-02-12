<?php

use yii\db\Migration;

/**
 * Class m210212_153651_customer_company_changed_table
 */
class m210212_153651_customer_company_changed_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customer_company_history', [
            'customer_company_history_id' => $this->primaryKey()->notNull(),
            'customer_id' => $this->integer(),
            'old_company_id' => $this->integer()->notNull(),
            'new_company_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'customer_company_history_customer_id_fk',
            'customer_company_history',
            'customer_id',
            'customer',
            'customer_id'
        );
        $this->addForeignKey(
            'customer_company_history_old_company_id_fk',
            'customer_company_history',
            'old_company_id',
            'company',
            'company_id'
        );
        $this->addForeignKey(
            'customer_company_history_new_company_id_fk',
            'customer_company_history',
            'new_company_id',
            'company',
            'company_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customer_company_history');
    }
    
}
