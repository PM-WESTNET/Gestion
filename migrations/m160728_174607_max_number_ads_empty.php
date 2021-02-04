<?php

use app\modules\config\models\Category;
use yii\db\Migration;
use yii\db\Query;

class m160728_174607_max_number_ads_empty extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {                
        $this->setConfig();
    }

    public function down()
    {
        echo "m160728_174607_max_number_ads_empty cannot be reverted.\n";

        return false;
    }
    
    private function setConfig(){
        $category= Category::findOne(['name' => 'General']);
        
        
        $this->insert('item', [
            'attr' => 'max_number_ads_empty',
            'type' => 'textInput',
            'label' => 'Numero de ultimo ADS impreso',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 0
        ]);
        
        $item_id= \app\modules\config\models\Item::find()->max('item_id');
        
        $this->insert('config', [
            'value' => '9900000',
            'item_id' => $item_id,           
        ]);
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
