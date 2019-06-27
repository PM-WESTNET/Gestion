<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190603_170606_add_config_item_for_baja_category extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Ticket']);

        $this->insert('item', [
            'attr' => 'baja-category-id',
            'type' => 'textInput',
            'label' =>"Categoría de baja",
            'description' => "Indica el id de la categoría de baja",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 15
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'd-product_id-extension-de-pago'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
