<?php

use yii\db\Migration;

class m160718_150001_mailing_config extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        $this->insert('category', [
            'name' => 'Notificaciones por Correo',
            'status' => 'enabled'
        ]);

        $categoryId = $this->db->getLastInsertID();


        $this->insert('item', [
            'attr' => 'phone-st',
            'type' => 'textInput',
            'label' => 'Teléfono servicio técnico',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => '0261 4 200997 - WhatsApp: 261 5087213'
        ]);

        $this->insert('item', [
            'attr' => 'phone-admin',
            'type' => 'textInput',
            'label' => 'Teléfono administración',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => '0261 4 200997 - WhatsApp: 261 6547474'
        ]);

        $this->insert('item', [
            'attr' => 'phone-sellers',
            'type' => 'textInput',
            'label' => 'Teléfono administración',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => '0261 4 294321 - WhatsApp: 261 6607841'
        ]);
        
        $this->insert('item', [
            'attr' => 'mail-top-title',
            'type' => 'textInput',
            'label' => 'Ante título mailing',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 'Westnet le informa'
        ]);

    }

    public function down()
    {
        echo "m160718_150001_mailing_config cannot be reverted.\n";

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
