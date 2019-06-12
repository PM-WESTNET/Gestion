<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190612_153636_add_config_item_ticket_category_solicitud_de_edicion_de_datos extends Migration
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
            'attr' => 'ticket-category-edicion-de-datos-id',
            'type' => 'textInput',
            'label' =>"Id de categoría de edición de datos",
            'description' => "Indica el id de la categoría de ticket solicitud de edición de datos",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 102
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'ticket-category-edicion-de-datos-id'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}