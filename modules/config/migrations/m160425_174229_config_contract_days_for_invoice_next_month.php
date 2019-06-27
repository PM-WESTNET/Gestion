<?php

use app\modules\config\models\Category;
use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

class m160425_174229_config_contract_days_for_invoice_next_month extends Migration
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

        if (empty($category)) {
            $this->insert('category', [
                'name' => 'Westnet',
                'status' => 'enabled'
            ]);
            $category_id = $this->db->lastInsertID;
        }else{
            $category_id = $category->category_id;
        }

        $this->insert('item', [
            'attr' => 'contract_days_for_invoice_next_month',
            'type' => 'textInput',
            'label' => 'Dias a fin de mes que postergan facturacion.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0,
            'default' => 0
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'contract_days_for_invoice_next_month']);
    }
}