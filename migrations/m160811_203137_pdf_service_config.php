<?php

use yii\db\Migration;
use app\modules\config\models\Category;

class m160811_203137_pdf_service_config extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        
        $category= Category::findOne(['name' => 'General']);
        
        if(!$category){
            throw new \yii\console\Exception('Category not found.');
        }
        
        $this->insert('item', [
            'attr' => 'wkhtmltopdf_docker_host',
            'type' => 'textInput',
            'label' => 'Host para servicio wkhtmltopdf ',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 'http://127.0.0.1/'
        ]);
        
        $this->insert('item', [
            'attr' => 'wkhtmltopdf_docker_port',
            'type' => 'textInput',
            'label' => 'Puerto para servicio wkhtmltopdf ',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => '5001'
        ]);
        
    }

    public function down()
    {
        echo "m160811_203137_pdf_service_config cannot be reverted.\n";

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
