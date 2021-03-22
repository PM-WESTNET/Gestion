<?php

use yii\db\Migration;
use app\modules\config\models\Category;

class m160731_222124_plan_increase_config extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        $category= Category::findOne(['name' => 'General']);
        
        $this->insert('item', [
            'attr' => 'months-without-increase',
            'type' => 'textInput',
            'label' => 'Meses sin aplicar aumento a clientes',
            'description' => 'Cantidad de meses durante lo cuales no se debe aumentar la tarifa de un cliente',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 4
        ]);
        
    }

    public function down()
    {
        echo "m160731_222124_plan_increase_config cannot be reverted.\n";

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
