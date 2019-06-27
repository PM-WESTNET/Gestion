<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190625_164747_add_config_item_for_payment_extension_duration_days extends Migration
{

    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Mobile App']);
//
//        $this->insert('item', [
//            'attr' => 'payment_extension_duration_days',
//            'type' => 'textInput',
//            'label' =>"Cantidad de días de extensión de pago",
//            'description' => "Indica la cantidad de días que durará la extension de pago que se solicite desde la aplicación",
//            'multiple' => 0,
//            'category_id' => $category->category_id,
//            'superadmin' => 0,
//            'default' => 5
//        ]);

        $this->insert('item', [
                    'attr' => 'payment_extension_duration_days_free',
                    'type' => 'textInput',
                    'label' =>"Cantidad de días de extensión de pago gratis",
                    'description' => "Indica la cantidad de días que durará la primer extension de pago que se solicite desde la aplicación (gratis)",
                    'multiple' => 0,
                    'category_id' => $category->category_id,
                    'superadmin' => 0,
                    'default' => 5
                ]);
//
//        $this->insert('item', [
//            'attr' => 'payment_extension_qty_per_month',
//            'type' => 'textInput',
//            'label' =>"Cantidad extensiones de pago por mes",
//            'description' => "Indica la cantidad de extensiones de pago que el cliente puede solicitar desde la aplicación",
//            'multiple' => 0,
//            'category_id' => $category->category_id,
//            'superadmin' => 0,
//            'default' => 2
//        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'payment_extension_duration_days'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();


        $item = Item::find()->where(['attr' => 'payment_extension_duration_days_free'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'payment_extension_qty_per_month'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
