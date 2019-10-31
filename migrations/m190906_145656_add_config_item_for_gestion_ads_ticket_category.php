<?php

use yii\db\Migration;
use app\modules\config\models\Category;

class m190906_145656_add_config_item_for_gestion_ads_ticket_category extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Ticket']);
        $gestion_category = \app\modules\ticket\models\Category::findOne(['slug' => 'gestion-de-ads']);

        $this->insert('item', [
            'attr' => 'ticket_category_gestion_ads',
            'type' => 'textInput',
            'label' =>"Id de categoría de Gestion de ADS",
            'description' => "Indica el id de la categoria de tickets para la categoria de gestion de ads",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => $gestion_category ? $gestion_category->category_id : 0
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'ticket_category_gestion_ads'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
