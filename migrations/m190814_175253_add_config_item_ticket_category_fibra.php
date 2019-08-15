<?php

use yii\db\Migration;
use app\modules\config\models\Category;

class m190814_175253_add_config_item_ticket_category_fibra extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Westnet']);
        $fibra_category = \app\modules\ticket\models\Category::findOne(['slug' => 'Instalaciones Fibra']);

        $this->insert('item', [
            'attr' => 'fibra_instalation_category_id',
            'type' => 'textInput',
            'label' =>"Id de categoría de instalación fibra en mesa",
            'description' => "Indica el id de la categoria de tickets para la categoria de instalaciones de fibra",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => $fibra_category ? $fibra_category->category_id : 0
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'fibra_instalation_category_id'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
