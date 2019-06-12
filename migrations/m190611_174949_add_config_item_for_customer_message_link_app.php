<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190611_174949_add_config_item_for_customer_message_link_app extends Migration
{

    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Customer']);

        $this->insert('item', [
            'attr' => 'link-to-app-customer-message-id',
            'type' => 'textInput',
            'label' =>"Id de la plantilla con link de aplicación",
            'description' => "Indica el id de customer message a usar para enviar mensaje informativo con link de descarga de la app",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 4
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'link-to-app-customer-message-id'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}