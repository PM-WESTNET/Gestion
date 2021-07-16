<?php

use yii\db\Migration;
use app\modules\sale\models\Company;
use app\modules\config\models\Category;

/**
 * Class m210716_141123_insert_siro_configuration_for_company_into_item_config_table
 */
class m210716_141123_insert_siro_configuration_for_company_into_item_config_table extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {   
        $this->AddColumnCompanyID();             
        $this->UpdatedConfiguration();
        $this->setConfigRedesDelOeste();
        $this->setConfigServicargas();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    private function AddColumnCompanyID(){
        $this->addColumn('{{%item}}', 'company_id', $this->integer());
    }


    private function UpdatedConfiguration(){
        $company = Company::findOne(['fantasy_name' => 'SISTECOM']);

        $this->update('{{%item}}', [
            'attr' => 'siro_username_las_heras',
            'label' => 'Usuario Siro - Las Heras',
            'company_id' => $company->company_id
        ], ['attr' => 'siro_username']);

        $this->update('{{%item}}', [
            'attr' => 'siro_password_las_heras',
            'label' => 'Password Siro - Las Heras',
            'company_id' => $company->company_id
        ],['attr' => 'siro_password']);

        $this->update('{{%item}}', [
            'attr' => 'siro_company_client_number_las_heras',
            'label' => 'Numero de cliente Siro - Las Heras',
            'company_id' => $company->company_id
        ], ['attr' => 'siro_company_client_number']);

    }

    private function setConfigRedesDelOeste(){
        $category= Category::findOne(['name' => 'Notificaciones']);
        $company = Company::findOne(['fantasy_name' => 'REDES DEL OESTE SA']);

        $this->insert('item', [
            'attr' => 'siro_username_redes_del_oeste',
            'type' => 'textInput',
            'label' => 'Usuario Siro - REDES DEL OESTE',
            'description' => 'UsuarioTestSiro',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0,
            'company_id' => $company->company_id
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);


        $this->insert('item', [
            'attr' => 'siro_password_redes_del_oeste',
            'type' => 'textInput',
            'label' => 'Password Siro - REDES DEL OESTE',
            'description' => 'Hola123',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0,
            'company_id' => $company->company_id
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);


        $this->insert('item', [
            'attr' => 'siro_company_client_number_redes_del_oeste',
            'type' => 'textInput',
            'label' => 'Numero de cliente Siro - REDES DEL OESTE',
            'description' => '1405150058293',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0,
            'company_id' => $company->company_id
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }

    private function setConfigServicargas(){
        $category= Category::findOne(['name' => 'Notificaciones']);
        $company = Company::findOne(['fantasy_name' => 'SERVICARGAS SA']);

        $this->insert('item', [
            'attr' => 'siro_username_servicargas',
            'type' => 'textInput',
            'label' => 'Usuario Siro - SERVICARGAS',
            'description' => 'UsuarioTestSiro',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0,
            'company_id' => $company->company_id
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);


        $this->insert('item', [
            'attr' => 'siro_password_servicargas',
            'type' => 'textInput',
            'label' => 'Password Siro - SERVICARGAS',
            'description' => 'Hola123',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0,
            'company_id' => $company->company_id
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);


        $this->insert('item', [
            'attr' => 'siro_company_client_number_servicargas',
            'type' => 'textInput',
            'label' => 'Numero de cliente Siro - SERVICARGAS',
            'description' => '1405150058293',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0,
            'company_id' => $company->company_id
        ]);
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '',
            'item_id' => $item_id,           
        ]);
    }




   
}
