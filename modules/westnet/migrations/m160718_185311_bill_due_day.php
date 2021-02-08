<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160718_185311_bill_due_day extends Migration
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
            'attr' => 'bill_due_day',
            'type' => 'textInput',
            'label' => 'Dia de vencimiento de factura.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'bill_due_day']);
    }
}
