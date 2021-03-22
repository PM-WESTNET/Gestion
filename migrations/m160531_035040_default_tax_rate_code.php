<?php

use yii\db\Migration;
use yii\db\Query;

class m160531_035040_default_tax_rate_code extends Migration
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
            ->where(['name' => 'Comprobantes'])
            ->scalar();

        $this->insert('item', [
            'attr' => 'default_tax_rate_code',
            'type' => 'textInput',
            'label' => 'Código AFIp de tasa impositiva por defecto',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 5
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'default_tax_rate_code']);
    }
}
