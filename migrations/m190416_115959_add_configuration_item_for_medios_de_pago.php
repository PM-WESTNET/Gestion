<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\config\models\Item;
use app\modules\config\models\Config;

class m190416_115959_add_configuration_item_for_medios_de_pago extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'ADS']);

        $this->insert('item', [
            'attr' => 'ads_payment_methods',
            'type' => 'textInput',
            'label' =>"Medios de pago",
            'description' => "Contenido que se mostrará bajo la leyenda 'Medios de pago'",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'Pago fácil, Ecopago.'
        ]);

        $category = Category::findOne(['name' => 'Comprobantes']);

        $this->insert('item', [
            'attr' => 'pdf_bill_payment_methods',
            'type' => 'textInput',
            'label' =>"Medios de pago",
            'description' => "Contenido que se mostrará bajo la leyenda 'Medios de pago' en el PDF de un comprobante",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'Pago fácil, Ecopago.'
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'ads_payment_methods'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'pdf_bill_payment_methods'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
