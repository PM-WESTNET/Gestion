<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160720_035645_instalation_category_id extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $this->createConfig();
    }

    public function down()
    {

        $this->deleteConfig();

        return false;
    }

    private function createConfig()
    {

        $category = Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'instalation_category_id',
            'type' => 'textInput',
            'label' => 'Categoría de instalación en Mesa.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'instalation_category_id']);
    }
}
