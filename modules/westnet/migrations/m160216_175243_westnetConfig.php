<?php

use yii\db\Schema;
use yii\db\Migration;

class m160216_175243_westnetConfig extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        $this->insert('category', [
            'name' => 'Westnet',
            'status' => 'enabled'
        ]);

        $categoryId = $this->db->getLastInsertID();


        $this->insert('item', [
            'attr' => 'default_cir',
            'type' => 'textInput',
            'label' => 'Cir',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 35
        ]);

        $this->insert('item', [
            'attr' => 'annual_availability',
            'type' => 'textInput',
            'label' => 'Disponibilidad Anual',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 96
        ]);
    }

    public function down()
    {
        $this->delete('item', ['attr' => 'default_cir']);
        $this->delete('item', ['attr' => 'annual_availability']);
        $this->delete('category', ['name' => 'Westnet']);

        return false;
    }
}
