<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;
use app\modules\ticket\models\TicketManagement;
use app\modules\ticket\models\Observation;

class m190924_105858_add_config_item_api_key_google_maps extends Migration
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
            'attr' => 'google_maps_api_key',
            'type' => 'textInput',
            'label' =>"API Key de Google Maps",
            'description' => "Indica el API Key de Google Maps",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'AIzaSyD1QmziL2ZbZT4EZsIbZObkOwOGt62ONCg'
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'google_maps_api_key'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
