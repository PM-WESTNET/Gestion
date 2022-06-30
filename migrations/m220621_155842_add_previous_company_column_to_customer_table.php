<?php

use yii\db\Migration;

/**
 * Handles adding previous_company to table `customer`.
 * Has foreign keys to the tables:
 *
 * - `customer_previous_company`
 */
class m220621_155842_add_previous_company_column_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'previous_company_id', $this->integer());

        // creates index for column `previous_company_id`
        $this->createIndex(
            'idx-customer-previous_company_id',
            'customer',
            'previous_company_id'
        );

        // add foreign key for table `customer_previous_company`
        $this->addForeignKey(
            'fk-customer-previous_company_id',
            'customer',
            'previous_company_id',
            'customer_previous_company',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `customer_previous_company`
        $this->dropForeignKey(
            'fk-customer-previous_company_id',
            'customer'
        );

        // drops index for column `previous_company_id`
        $this->dropIndex(
            'idx-customer-previous_company_id',
            'customer'
        );

        $this->dropColumn('customer', 'previous_company_id');
    }
}
