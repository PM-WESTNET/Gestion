<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 10:25
 */

use yii\db\Migration;
use app\modules\config\models\Category;

class m190228_102424_add_config_item extends Migration
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
            'attr' => 'is_developer_mode',
            'type' => 'checkbox',
            'label' =>"¿Esta en modo de desarrollador?",
            'description' => "Si esta activado evita ciertas acciones como la comunicacion con mesa al momento de crear un contrato",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
    }
}