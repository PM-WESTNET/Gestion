<?php

use yii\db\Schema;
use yii\db\Migration;

class m150919_215035_config_stock_managment extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        
        $this->insert('category', [
            'name' => 'Gestión de Stock',
            'status' => 'enabled'
        ]);
        
        $categoryId = $this->db->getLastInsertID();
        
        //Vencimiento por defecto de pedidos y presupuestos
        $this->insert('item', [
            'attr' => 'strict_stock',
            'type' => 'checkbox',
            'label' => 'Stock estricto (no se permite stock negativo)',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => false
        ]);
        
        //Vencimiento por defecto de pedidos y presupuestos
        $this->insert('item', [
            'attr' => 'enable_secondary_stock',
            'type' => 'checkbox',
            'label' => 'Habilitar stock secundario',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => true
        ]);
        
    }
    
    public function down()
    {
        echo "m150919_215034_config_order cannot be reverted.\n";

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
