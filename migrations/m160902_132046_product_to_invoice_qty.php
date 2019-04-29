<?php

use yii\db\Migration;

class m160902_132046_product_to_invoice_qty extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE product_to_invoice ADD qty INT DEFAULT 1 NULL;');
        $this->execute('update product_to_invoice
                        left join contract_detail cd on product_to_invoice.contract_detail_id = cd.contract_detail_id
                        left join contract c on cd.contract_id = c.contract_id
                        set qty = cd.count, amount = amount / cd.count
                        where product_to_invoice.contract_detail_id is not null and cd.count > 1');
    }

    public function down()
    {
        echo "m160902_132046_product_to_invoice_qty cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
