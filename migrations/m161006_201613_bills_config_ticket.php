<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m161006_201613_bills_config_ticket extends Migration
{
    
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        
        $category= Category::findOne(['name' => 'Ticket']);
        
        $this->insert('item', [
            'attr' => 'credit-bill-category-id',
            'type' => 'textInput',
            'label' => 'ID Categoria de ticket de nota de credito',
            'description' => 'Indica el ID de la categoria de nota de credito',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 10
        ]);
        
        $this->insert('item', [
            'attr' => 'bill-category-id',
            'type' => 'textInput',
            'label' => 'ID Categoria de ticket de factura',
            'description' => 'Indica el ID de la categoria de factura',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 0
        ]);
        
    }

    public function down()
    {
        echo "m161006_201613_bills_config_ticket cannot be reverted.\n";

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
