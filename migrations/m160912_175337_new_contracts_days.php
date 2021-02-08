<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160912_175337_new_contracts_days extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up() {
        $category = Category::findOne(['name' => 'Westnet']);

        if (!$category) {
            throw new Exception('Category not found.');
        }

        $this->insert('item', [
            'attr' => 'new_contracts_days',
            'type' => 'textInput',
            'label' => 'Dias de contrato nuevo.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 0
        ]);
    }

    public function down() {
        echo "m160912_175337_new_contracts_days cannot be reverted.\n";

        return false;
    }
}
