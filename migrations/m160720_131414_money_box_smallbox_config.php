<?php

use app\modules\config\models\Category;
use yii\db\Migration;
use yii\db\Query;

class m160720_131414_money_box_smallbox_config extends Migration
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
            ->where(['name' => 'Contabilidad'])->scalar();

        $this->insert('item', [
            'attr' => 'money_box_smallbox',
            'type' => 'textInput',
            'label' => 'Tipo de entidad CAJA.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 3,
        ]);

        $categoryId = (new Query())
            ->select('category_id')
            ->from('category')
            ->where(['name' => 'Contabilidad'])->scalar();

        $this->insert('item', [
            'attr' => 'payment_method_cash',
            'type' => 'textInput',
            'label' => 'Metodo de pago - Contado',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 1,
        ]);
    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'money_box_smallbox']);
        $this->delete('item', ['attr' => 'payment_method_cash']);
    }
}
