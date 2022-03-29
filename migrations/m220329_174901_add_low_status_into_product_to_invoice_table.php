<?php

use yii\db\Migration;

/**
 * Class m220329_174901_add_low_status_into_product_to_invoice_table
 */
class m220329_174901_add_low_status_into_product_to_invoice_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('product_to_invoice', 'status', "ENUM('active','consumed','canceled','low')");
    }
    public function safeDown()
    {
        $this->alterColumn('product_to_invoice', 'status', "ENUM('active','consumed','canceled')");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220329_174901_add_low_status_into_product_to_invoice_table cannot be reverted.\n";

        return false;
    }
    */
}
