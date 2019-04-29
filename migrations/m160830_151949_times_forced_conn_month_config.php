<?php

use app\modules\config\models\Category;
use yii\console\Exception;
use yii\db\Migration;

class m160830_151949_times_forced_conn_month_config extends Migration
{
    public function init(){
        $this->db = 'dbconfig';
        parent::init();      
    }

    public function up()
    {
        $category= Category::findOne(['name' => 'Westnet']);
        
        if(!$category){
            throw new Exception('Category not found.');
        }
        
        $this->insert('item', [
            'attr' => 'times_forced_conn_month',
            'type' => 'textInput',
            'label' => 'Cantidad de veces que se puede forzar una conexion en un mes',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 2
        ]);
        
        
    }

    public function down()
    {
        echo "m160830_151949_times_forced_conn_month_config cannot be reverted.\n";

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
