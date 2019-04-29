<?php

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

class m160524_155847_money_box_bank extends Migration
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

        $categoryId = (new Query())
            ->select('category_id')
            ->from('category')
            ->where(['name' => 'General'])->scalar();

        $this->insert('item', [
            'attr' => 'money_box_bank',
            'type' => 'textInput',
            'label' => 'Tipo de entidad bancaria.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 0
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'money_box_bank']);
    }
}
