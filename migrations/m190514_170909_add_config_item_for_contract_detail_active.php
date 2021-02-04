<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190514_170909_add_config_item_for_contract_detail_active extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'id-product_id-extension-de-pago',
            'type' => 'textInput',
            'label' =>"Indica el id del producto extension de pago",
            'description' => "Indica el id del producto extension de pago, se utiliza para que cuando se agrega el adiccional al contrato, éste se agregue automáticamente en estado activo",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
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
