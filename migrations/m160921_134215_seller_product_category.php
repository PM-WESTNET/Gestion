<?php

use yii\db\Migration;

class m160921_134215_seller_product_category extends Migration
{
    public function up()
    {
        $this->insert('category', [
            'name' => 'Producto para vendedores',
            'status' => 'enabled',
            'system' => 'seller-product',            
        ]);
    }

    public function down()
    {
        echo "m160921_134215_seller_product_category cannot be reverted.\n";

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
