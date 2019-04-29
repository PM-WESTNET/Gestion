<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/12/18
 * Time: 16:07
 */

use yii\db\Migration;
use app\modules\config\models\Category;

class m190108_113712_add_configuration_item_add_retenciones_into_report extends Migration
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
            'attr' => 'add_retenciones_into_in_out_report',
            'type' => 'checkbox',
            'label' =>"¿Añadir item 'Retenciones' en reporte?",
            'description' => "Indica si se va a añadir el item 'Retenciones' en el reporte 'Ingresos y Egresos'",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 1
        ]);
    }
}