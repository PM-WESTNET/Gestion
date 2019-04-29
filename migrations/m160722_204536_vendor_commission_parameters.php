<?php

use yii\db\Migration;

class m160722_204536_vendor_commission_parameters extends Migration
{
    
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        $this->insert('category', [
            'name' => 'Vendedores',
            'status' => 'enabled'
        ]);

        $categoryId = $this->db->getLastInsertID();

        $this->insert('item', [
            'attr' => 'payed_months_before_penalty',
            'type' => 'textInput',
            'label' => 'Meses con cliente activo requeridos',
            'description' => 'Cantidad de meses que un cliente debe pagar para que el vendedor no sea sancionado',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 3
        ]);

    }

    public function down()
    {
        echo 'No quiero... :p';
        
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
