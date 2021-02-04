<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/12/18
 * Time: 16:07
 */

use yii\db\Migration;
use app\modules\config\models\Category;

class m181228_160707_add_configuration_item extends Migration
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
            'attr' => 'general_address',
            'type' => 'textInput',
            'label' => 'Dirección genérica',
            'description' => 'Usada por ejemplo en los pdf de comprobantes como "Puede retirar su factura en: "',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '9 de julio 1257 Of.108  - P10'
        ]);
    }
}