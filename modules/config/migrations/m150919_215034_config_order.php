<?php

use yii\db\Schema;
use yii\db\Migration;

class m150919_215034_config_order extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        
        $this->insert('category', [
            'name' => 'Comprobantes',
            'status' => 'enabled'
        ]);
        
        $categoryId = $this->db->getLastInsertID();
        
        //Vencimiento por defecto de pedidos y presupuestos
        $this->insert('item', [
            'attr' => 'bill_default_expiration_days',
            'type' => 'textInput',
            'label' => 'Días por defecto para vencimiento de órden de venta',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 14
        ]);
        
        $itemId = $this->db->getLastInsertID();
        
        $this->insert('rule', [
            'message' => '',
            'max' => null,
            'min' => 0,
            'pattern' => null,
            'format' => null,
            'targetAttribute' => null,
            'targetClass' => null,
            'item_id' => $itemId,
            'validator' => 'integer'
        ]);
        
        //Forzar empresa de usuario
        $this->insert('item', [
            'attr' => 'force_customer_company',
            'type' => 'checkbox',
            'label' => 'Forzar la utilización de la empresa asociada al cliente',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => false
        ]);
        
        //Columna de verificacion (x) para remito
        $this->insert('item', [
            'attr' => 'show_delivery_note_verification_column',
            'type' => 'checkbox',
            'label' => 'Mostrar columna de verificación en remito',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => true
        ]);
        
        //Mostrar precios en remito
        $this->insert('item', [
            'attr' => 'show_price_delivery_note',
            'type' => 'checkbox',
            'label' => 'Mostrar precios en remito',
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
