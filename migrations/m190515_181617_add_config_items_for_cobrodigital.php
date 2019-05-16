<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190515_181617_add_config_items_for_cobrodigital extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $this->insert('category', [
            'name' => 'Cobro digital',
            'status' => 'enabled',
        ]);

        $category_id = $this->db->getLastInsertID();

        $this->insert('item', [
            'attr' => 'cobrodigital-url',
            'type' => 'textInput',
            'label' =>"Url al ws de Cobro Digital",
            'description' => "Indica la url del WebService de Cobro Digital",
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0,
            'default' => 0
        ]);

        $this->insert('item', [
            'attr' => 'cobrodigital-user',
            'type' => 'textInput',
            'label' =>"Usuario para ws de Cobro Digital",
            'description' => "Indica el usuario del WebService de Cobro Digital",
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0,
            'default' => 0
        ]);

        $this->insert('item', [
            'attr' => 'cobrodigital-password',
            'type' => 'textInput',
            'label' =>"Contraseña para ws de Cobro Digital",
            'description' => "Indica la contraseña del WebService de Cobro Digital",
            'multiple' => 0,
            'category_id' => $category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'cobrodigital-password'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'cobrodigital-user'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'cobrodigital-url'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $category = Category::findOne(['name' => 'Cobro digital']);
        $category->delete();
    }
}
