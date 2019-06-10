<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190610_1622724_add_config_item_month_qty_to_declare_app_uninstalled extends Migration
{

    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Mobile App']);

        $this->insert('item', [
            'attr' => 'month-qty-to-declare-app-uninstalled',
            'type' => 'textInput',
            'label' =>"Meses para declarar la app desintalada",
            'description' => "Indica la cantidad de meses que debe pasar sin actividad un cliente para declarar que se ha desintalado la app. Expresado en meses",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 3
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'month-qty-to-declare-app-uninstalled'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}