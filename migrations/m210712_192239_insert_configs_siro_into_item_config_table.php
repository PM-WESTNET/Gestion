<?php

use app\modules\config\models\Category;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m210712_192239_insert_configs_siro_into_item_config_table
 */
class m210712_192239_insert_configs_siro_into_item_config_table extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {                
        $this->setConfigUsername();
        $this->setConfigPassword();
        $this->setConfigUrlGetToken();
        $this->setConfigCompanyClientNumber();
        $this->setConfigSiroInvoiceConcept();
        $this->setConfigSiroUrlOk();
        $this->setConfigSiroUrlError();
        $this->setConfigSiroUrlCreatePaymentIntention();
        $this->setConfigSiroUrlSearchPaymentIntention();
        $this->setConfigSiroExpiryTime();
        $this->setConfigSiroCommunicationBankRoela();
    }

    public function down()
    {
        echo "m160728_174607_max_number_ads_empty cannot be reverted.\n";

        return false;
    }
    
    private function setConfigUsername(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_username',
            'type' => 'textInput',
            'label' => 'Usuario Siro',
            'description' => 'UsuarioTestSiro',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigPassword(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_password',
            'type' => 'textInput',
            'label' => 'Password Siro',
            'description' => 'Hola123',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigUrlGetToken(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_url_get_token',
            'type' => 'textInput',
            'label' => 'URL Obtencion de Token',
            'description' => 'https://srvwebhomologa.bancoroela.com.ar:44443/api/Sesion',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigCompanyClientNumber(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_company_client_number',
            'type' => 'textInput',
            'label' => 'Numero de cliente Siro',
            'description' => '1405150058293',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroInvoiceConcept(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_invoice_concept',
            'type' => 'textInput',
            'label' => 'Concepto de Intencion de Pago',
            'description' => 'Saldo Codigo @Cliente',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroUrlOk(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_url_ok',
            'type' => 'textInput',
            'label' => 'URL Retorno OK',
            'description' => 'http://gestiontest.westnet.com.ar/westnet/notifications/notification/success-bank-roela',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroUrlError(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_url_error',
            'type' => 'textInput',
            'label' => 'URL Retorno Error',
            'description' => 'http://gestiontest.westnet.com.ar/westnet/notifications/notification/error',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroUrlCreatePaymentIntention(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_url_create_payment_intention',
            'type' => 'textInput',
            'label' => 'URL Creacion de Intencion de Pago',
            'description' => 'https://srvwebhomologa.bancoroela.com.ar:44443/api/Pago/',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroUrlSearchPaymentIntention(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_url_search_payment_intention',
            'type' => 'textInput',
            'label' => 'URL Creacion de Intencion de Pago',
            'description' => 'https://srvwebhomologa.bancoroela.com.ar:44443/api/Pago/',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroExpiryTime(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_expiry_time',
            'type' => 'textInput',
            'label' => 'Tiempo de Expiracion Hash',
            'description' => '10',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigSiroCommunicationBankRoela(){
        $category= Category::findOne(['name' => 'Notificaciones']);
    
        $this->insert('item', [
            'attr' => 'siro_communication_bank_roela',
            'type' => 'textInput',
            'label' => 'Habilitar Comunicacion Banco Roela',
            'description' => '0',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 0
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210712_192239_insert_configs_siro_into_item_config_table cannot be reverted.\n";

        return false;
    }
    */
}
