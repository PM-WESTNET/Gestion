<?php

use yii\db\Migration;
use app\modules\config\models\Category;

class m190927_170606_add_config_item_limit_days_to_create_provider_bill extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Comprobantes']);

        $this->insert('item', [
            'attr' => 'limit_days_to_create_provider_bill',
            'type' => 'textInput',
            'label' =>"Limite de dias para comprobante a proveedor",
            'description' => "Numero entero. Determina la fecha mínima en la que puede cargar un comprobante a provedor, restandole a la fecha actual, la cantidad de dias indicados",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 45
        ]);
    }


    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'limit_days_to_create_provider_bill'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }
}
