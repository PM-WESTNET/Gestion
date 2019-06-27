<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 21/03/19
 * Time: 17:03
 */

use yii\db\Migration;

class m190321_170202_ticket_base_tables_for_estatus_actions extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('action',[
            'action_id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string()
        ]);

        $this->createTable('status_has_action', [
            'status_has_action_id' => $this->primaryKey(),
            'status_id' => $this->integer(),
            'action_id' => $this->integer(),
            'text_1' => $this->string(),
            'text_2' => $this->text(),

            //Elementos individuales de cada clase.
            'ticket_category_id' => $this->integer(),
            'task_category_id' => $this->integer(),
            'task_type_id' => $this->integer()
        ]);
        
        $this->addForeignKey('fk_status_has_action_status_id', 'status_has_action', 'status_id', 'status', 'status_id');
        $this->addForeignKey('fk_status_has_action_action_id', 'status_has_action', 'action_id', 'action', 'action_id');
        $this->addForeignKey('fk_status_has_action_ticket_category_id', 'status_has_action', 'ticket_category_id', 'category', 'category_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_status_has_action_ticket_category_id', 'status_has_action');
        $this->dropForeignKey('fk_status_has_action_action_id', 'status_has_action');
        $this->dropForeignKey('fk_status_has_action_status_id', 'status_has_action');

        $this->dropTable('status_has_action');
        $this->dropTable('action');
    }
}