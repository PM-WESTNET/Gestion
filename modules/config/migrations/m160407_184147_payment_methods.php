<?php

use app\modules\config\models\Category;
use yii\db\Schema;
use yii\db\Migration;
use yii\db\Query;

class m160407_184147_payment_methods extends Migration
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

        $category = Category::findOne(['name' => 'General']);

        if (empty($category)) {
            $this->insert('category', [
                'name' => 'General',
                'status' => 'enabled'
            ]);
            $category_id = $this->db->lastInsertID;
        }else{
            $category_id = $category->category_id;
        }

        $this->insert('item', [
            'attr' => 'payment_method_paycheck',
            'type' => 'textInput',
            'label' => 'Metodo de pago - Cheque',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0,
            'default' => 0
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'payment_method_paycheck']);
    }
}