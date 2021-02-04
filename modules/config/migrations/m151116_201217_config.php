<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_201217_config extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();

    }
    
    public function safeUp()
    {
        
        $this->insert('category', [
            'name' => 'Media',
            'status' => 'enabled'
        ]);
        
        $categoryId = $this->db->getLastInsertID();
        
        /************** 
         ** Imagenes **
         *************/
        
        //Ancho min
        $this->insert('item', [
            'attr' => 'image_min_width',
            'type' => 'textInput',
            'label' => 'Ancho mínimo de imagenes en pixeles',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 200
        ]);
        
        $itemId = $this->db->getLastInsertID();
        
        $this->insert('rule', [
            'message' => '',
            'max' => 4000,
            'min' => 1,
            'pattern' => null,
            'format' => null,
            'targetAttribute' => null,
            'targetClass' => null,
            'item_id' => $itemId,
            'validator' => 'integer'
        ]);
        
        //Alto min
        $this->insert('item', [
            'attr' => 'image_min_height',
            'type' => 'textInput',
            'label' => 'Alto mínimo de imagenes en pixeles',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 200
        ]);
        
        $itemId = $this->db->getLastInsertID();
        
        $this->insert('rule', [
            'message' => '',
            'max' => 4000,
            'min' => 1,
            'pattern' => null,
            'format' => null,
            'targetAttribute' => null,
            'targetClass' => null,
            'item_id' => $itemId,
            'validator' => 'integer'
        ]);
        
        //Ancho max
        $this->insert('item', [
            'attr' => 'image_max_width',
            'type' => 'textInput',
            'label' => 'Ancho máximo de imagenes en pixeles',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 1920
        ]);
        
        $itemId = $this->db->getLastInsertID();
        
        $this->insert('rule', [
            'message' => '',
            'max' => 5000,
            'min' => 1,
            'pattern' => null,
            'format' => null,
            'targetAttribute' => null,
            'targetClass' => null,
            'item_id' => $itemId,
            'validator' => 'integer'
        ]);
        
        //Alto max
        $this->insert('item', [
            'attr' => 'image_max_height',
            'type' => 'textInput',
            'label' => 'Alto máximo de imagenes en pixeles',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 1920
        ]);
        
        $itemId = $this->db->getLastInsertID();
        
        $this->insert('rule', [
            'message' => '',
            'max' => 5000,
            'min' => 1,
            'pattern' => null,
            'format' => null,
            'targetAttribute' => null,
            'targetClass' => null,
            'item_id' => $itemId,
            'validator' => 'integer'
        ]);
        
        //Alto max
        $this->insert('item', [
            'attr' => 'image_quality',
            'type' => 'textInput',
            'label' => 'Calidad de imagen',
            'description' => 'Valor entre 0 y 1, siendo 1 la máxima calidad',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 0.8
        ]);
        
        $itemId = $this->db->getLastInsertID();
        
        $this->insert('rule', [
            'message' => '',
            'max' => 1,
            'min' => 0,
            'pattern' => null,
            'format' => null,
            'targetAttribute' => null,
            'targetClass' => null,
            'item_id' => $itemId,
            'validator' => 'double'
        ]);
        
        //Modo de generacion de miniaturas
        $this->insert('item', [
            'attr' => 'image_thumbnail_mode_inset',
            'type' => 'checkbox',
            'label' => 'Al generar miniatura, mantener relación de aspecto original',
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
