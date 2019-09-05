<?php

use yii\db\Migration;

class m190904_130606_add_ticket_management_fields extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('ticket_management', 'by_wp', $this->boolean());
        $this->addColumn('ticket_management', 'by_sms', $this->boolean());
        $this->addColumn('ticket_management', 'by_email', $this->boolean());
        $this->addColumn('ticket_management', 'by_call', $this->boolean());
    }

    public function safeDown()
    {
        $this->dropColumn('ticket_management', 'by_call');
        $this->dropColumn('ticket_management', 'by_email');
        $this->dropColumn('ticket_management', 'by_sms');
        $this->dropColumn('ticket_management', 'by_wp');
    }
}
