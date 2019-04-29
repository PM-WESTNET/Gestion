<?php

use yii\db\Migration;

class m181016_124414_add_config_item_integratech extends Migration
{
    public function init(){
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $this->insert('category', [
            'name' => 'Notificaciones',
            'status' => 'enabled',
            'superadmin' => 0
        ]);
        
        $category_id = $this->db->getLastInsertID();
        
        $this->insert('item', [
            'attr' => 'integratech_username',
            'type' => 'textInput',
            'default' => '',
            'label' => 'Nombre de usuario de Itegratech',
            'description' => 'Nombre de usuario para el servicio de integratech',
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0
        ]);
        
        $this->insert('item', [
            'attr' => 'integratech_password',
            'type' => 'textInput',
            'default' => '',
            'label' => 'Contraseña de usuario de Itegratech',
            'description' => 'Contraseña de usuario para el servicio de integratech',
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0
        ]);
        
        $this->insert('item', [
            'attr' => 'integratech_url',
            'type' => 'textInput',
            'default' => '',
            'label' => 'Url de servicio Itegratech',
            'description' => 'Url de servicio de Integratech desde donde se van a hacer envios de los SMS',
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0
        ]);
    }

    public function safeDown()
    {
        $this->delete('item', ['attr' => 'integratech_url']);
        $this->delete('item', ['attr' => 'integratech_password']);
        $this->delete('item', ['attr' => 'integratech_username']);
        $this->delete('category', ['name' => 'Notificaciones']);
    }

}
