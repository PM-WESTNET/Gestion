<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m161109_145538_referenced_discount extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {

        $category = Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'referenced_discount',
            'type' => 'textInput',
            'label' => 'Descuento por defecto para refenciados',
            'description' => 'Descuento por defecto para refenciados',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 0
        ]);
    }

    public function down()
    {
        echo "m161109_145538_referenced_discount cannot be reverted.\n";

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
