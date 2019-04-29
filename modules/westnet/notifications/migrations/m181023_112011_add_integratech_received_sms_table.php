<?php

use yii\db\Migration;

class m181023_112011_add_integratech_received_sms_table extends Migration
{
    public function init(){
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('integratech_received_sms', [
            'integratech_received_sms_id' => $this->primaryKey(),
            'destaddr' => $this->text(45),
            'charcode' => $this->text(45),
            'sourceaddr' => $this->text(),
            'message' => $this->text(),
            'customer_id' => $this->integer(),
            'ticket_id' => $this->integer()
        ]);

        $this->createTable('integratech_sms_filter', [
            'integratech_sms_filter_id' => $this->primaryKey(),
            'word' => $this->text(45),
            'action' => "ENUM('Delete', 'Create Ticket')",
            'status' => "ENUM('enabled', 'disabled')",
            'category_id' => $this->integer(),
            'is_created_automaticaly' => $this->boolean()
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('sms_filter');
        $this->dropTable('integratech_received_sms');
    }
}
