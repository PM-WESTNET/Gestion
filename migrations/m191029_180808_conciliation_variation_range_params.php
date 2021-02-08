<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

/**
 * Class m191029_180808_conciliation_variation_range_params
 */
class m191029_180808_conciliation_variation_range_params extends Migration
{

    public function init()
    {
        $this->db = 'dbconfig';
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Contabilidad']);


        $this->insert('item', [
            'attr' => 'movements_items_range',
            'type' => 'textInput',
            'label' =>"Diferecia entre Item de Resumen y Movimiento",
            'description' => "Diferencia de saldos entre item y movimiento para permitir conciliar",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0.99
        ]);

        $this->insert('item', [
            'attr' => 'diference_balance_on_close',
            'type' => 'textInput',
            'label' =>"Diferecia entre Saldo Real y Saldo Conciliado",
            'description' => "Diferencia de saldo real y saldo conciliado para poder cerrar conciliacion",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 10.00
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'movements_items_range'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();

        $item = Item::find()->where(['attr' => 'diference_balance_on_close'])->one();
        $configs = Config::find()->where(['item_id' => $item->item_id])->all();

        foreach ($configs as $config) {
            $config->delete();
        }
        $item->delete();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191029_180808_conciliation_variation_range_params cannot be reverted.\n";

        return false;
    }
    */
}
