<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190809_160808_add_config_item_payment_extension_real_duration_days extends Migration
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
            'attr' => 'payment_extension_real_duration_days',
            'type' => 'textInput',
            'label' =>"Cantidad de días real de extensión de pago",
            'description' => "Indica la cantidad de días que durará realmente la extension de pago que se solicite desde la aplicación (No es la misma fecha notificada al cliente, debería tener mas dias que la que se le notifica al cliente)",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 7
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'payment_extension_real_duration_days'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
