<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 25/03/19
 * Time: 12:21
 */

use yii\db\Migration;

class m190325_122020_add_generate_action_field_into_status_table extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('status', 'generate_action', $this->boolean());

        $this->update('status', ['generate_action' => 0]);
    }

    public function safeDown()
    {
        $this->dropColumn('status', 'generate_action');
    }
}