<?php

use app\modules\config\models\Category;
use yii\console\Exception;
use yii\db\Migration;

class m160909_142259_app_testing_config_item extends Migration {

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up() {
        $category = Category::findOne(['name' => 'General']);

        if (!$category) {
            throw new Exception('Category not found.');
        }

        $this->insert('item', [
            'attr' => 'app_testing',
            'type' => 'checkbox',
            'label' => 'Está la aplicación en modo testing?',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => false
        ]);
    }

    public function down() {
        $this->delete('item', [
            'attr' => 'app_testing',
        ]);
    }

}
