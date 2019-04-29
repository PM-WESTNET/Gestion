<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160908_205829_new_contract_days extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {

        $category= Category::findOne(['name' => 'Westnet']);

        if(!$category){
            throw new \yii\console\Exception('Category not found.');
        }

        $this->insert('item', [
            'attr' => 'new_contracts_days',
            'type' => 'textInput',
            'label' => 'Dias de Contrato Nuevo antes de Corte',
            'description' => 'Cantidad de dias despues de la creacion de contrato que no se deshabilita la conexion',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => '0'
        ]);

    }

    public function down()
    {
        echo "m160908_205829_new_contract_days cannot be reverted.\n";

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