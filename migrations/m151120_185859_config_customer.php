<?php

use yii\db\Schema;
use yii\db\Migration;

class m151120_185859_config_customer extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        $this->insert('category', [
            'name' => 'Customer',
            'status' => 'enabled'
        ]);

        $categoryId = $this->db->getLastInsertID();

        $this->insert('item', [
            'attr' => 'customer_address_required',
            'type' => 'textInput',
            'label' => 'Requerir domicilio en carga de cliente',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 1
        ]);

    }

    public function down()
    {
        $this->delete('item', ['attr' => 'customer_address_required']);
        $this->delete('category', ['name' => 'Customer']);

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
