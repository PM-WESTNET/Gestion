<?php

use yii\db\Migration;

class m160711_230119_default_seller_plan_category extends Migration
{
    public function up()
    {
        
        $this->insert('category', [
            'name' => 'Plan por defecto para vendedores',
            'status' => 'enabled',
            'system' => 'default-seller-plan'
        ]);
        
        $this->insert('category', [
            'name' => 'Plan para vendedores',
            'status' => 'enabled',
            'system' => 'seller-plan'
        ]);
        
    }

    public function down()
    {
        
        $this->delete('category', 'system="default-seller-plan"');
        $this->delete('category', 'system="seller-plan"');
        
        return true;
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
