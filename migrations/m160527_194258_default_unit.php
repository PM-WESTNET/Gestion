<?php

use yii\db\Migration;
use yii\db\Query;

class m160527_194258_default_unit extends Migration
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
            ->where(['name' => 'Productos'])
            ->scalar();

        $this->insert('item', [
            'attr' => 'default_unit_id',
            'type' => 'textInput',
            'label' => 'Unidad por defecto..',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 0
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'default_unit_id']);
    }
}
