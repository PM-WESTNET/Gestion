<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 11:59
 */

use yii\db\Migration;

class m190228_115959_add_phone4_field_into_customer_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('customer', 'phone4', $this->text(45));
    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'phone4');
    }

}