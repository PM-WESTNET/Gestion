<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 11/03/19
 * Time: 18:00
 */

use yii\db\Migration;
use app\modules\config\models\Item;
use app\modules\config\models\Config;
use app\modules\config\models\Category;

class m190311_180000_add_config_value_for_customer_last_update extends Migration
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
            'attr' => 'require_update_customer_data',
            'type' => 'textInput',
            'label' =>"Indica cada cuántos meses se va a requerir una actualización",
            'description' => "Indica cada cuántos meses va a requerir una actualización de los datos del cliente. Tiempo expresado en meses",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 24
        ]);
    }

    public function safeDown()
    {
        $item = Item::find()->where(['attr' => 'require_update_customer_data'])->one();

        $configs = Config::find()->where(['item_id'])->all();

        foreach ($configs as $config) {
            $config->delete();
        }

        $item->delete();
    }
}