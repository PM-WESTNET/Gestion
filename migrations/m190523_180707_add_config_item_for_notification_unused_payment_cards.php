<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190523_180707_add_config_item_for_notification_unused_payment_cards extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'General']);

        $this->insert('item', [
            'attr' => 'min-unused-payment-cards-qty-notification',
            'type' => 'textInput',
            'label' => "Cantidad en la que se creará un recordatorio",
            'description' => "Se mostrará una notificación cuando la cantidad de las tarjetas de cobro disponibles sea menor al valor",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 500
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'min-unused-payment-cards-qty-notification'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
