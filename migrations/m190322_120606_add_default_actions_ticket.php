<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 22/03/19
 * Time: 12:06
 */

use yii\db\Migration;

class m190322_120606_add_default_actions_ticket extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('action', 'type',"ENUM('ticket', 'event')");

        $this->insert('action', [
            'name' => 'Crear ticket baja derivada de cobranza',
            'slug' => 'crear-ticket-baja-derivada-de-cobranza',
            'type' => 'ticket'
        ]);

        $this->insert('action', [
            'name' => 'Crear evento de cobranza en agenda',
            'slug' => 'crear-evento-de-cobranza-en-agenda',
            'type' => 'event'
        ]);

        $this->addColumn('status_has_action','ticket_status_id', $this->integer());
        $this->addColumn('status_has_action','task_status_id', $this->integer());
        $this->addColumn('status_has_action','task_priority', $this->integer());
        $this->addColumn('status_has_action','task_time', $this->integer());
    }

    public function safeDown()
    {

        $this->dropColumn('action', 'type');

        $this->delete('action', [
            'slug' => 'crear-ticket-baja-derivada-de-cobranza',
        ]);

        $this->delete('action', [
            'slug' => 'Crear evento de cobranza en agenda',
        ]);
    }
}