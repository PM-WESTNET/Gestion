<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_201220_config_product_media extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function safeUp()
    {
        
        $this->insert('category', [
            'name' => 'Productos',
            'status' => 'enabled'
        ]);
        
        $categoryId = $this->db->getLastInsertID();
        
        /************** 
         ** Imagenes **
         *************/
        
        //Ancho min
        $this->insert('item', [
            'attr' => 'sale_products_list_view',
            'type' => 'textInput',
            'label' => '¿Mostrar imágenes en la lista de productos al generar un comprobante?',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 1
        ]);
          
    }
    
    public function safeDown()
    {
        echo "m151116_201217_config cannot be reverted.\n";

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
