<?php

use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\Permission;

class m190531_143030_add_ticket_management_table extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('ticket_management', [
            'ticket_management_id' => $this->primaryKey(),
            'ticket_id' => $this->integer(),
            'user_id' => $this->integer(),
            'timestamp' => $this->string(),
        ]);

        $this->addForeignKey('fk_ticket_management_ticket_id', 'ticket_management', 'ticket_id', 'ticket', 'ticket_id');
    }

    public function safeDown()
    {
        $this->dropTable('ticket_management');
    }
}
