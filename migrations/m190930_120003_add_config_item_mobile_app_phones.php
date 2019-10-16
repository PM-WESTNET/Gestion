<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;
use app\modules\ticket\models\TicketManagement;
use app\modules\ticket\models\Observation;

class m190930_120003_add_config_item_mobile_app_phones extends Migration
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
            'attr' => 'app_atencion_general_phone1',
            'type' => 'textInput',
            'label' =>"Teléfono de atención general",
            'description' => "Teléfono de atencion general 1",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '4200997'
        ]);

        $this->insert('item', [
            'attr' => 'app_atencion_general_phone2',
            'type' => 'textInput',
            'label' =>"Teléfono 2 de atención general",
            'description' => "Teléfono de atencion general 2",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '4055303'
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'app_atencion_general_phone2'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'app_atencion_general_phone1'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
