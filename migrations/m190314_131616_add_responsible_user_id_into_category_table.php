<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 14/03/19
 * Time: 13:17
 */

use yii\db\Migration;

class m190314_131616_add_responsible_user_id_into_category_table extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('category', 'responsible_user_id', $this->integer());
    }
    public function safeDown()
    {
        $this->dropColumn('category', 'responsible_user_id');
    }
}