<?php

use yii\db\Migration;

class m181025_123211_add_statuses_into_notification_table extends Migration
{
    public function init(){
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->alterColumn('notification', 'status', "ENUM('created', 'enabled', 'disabled', 'pending','sent','error','cancelled', 'in_process', 'timeout')");
    }
    public function safeDown()
    {
        $this->alterColumn('notification', 'status', "ENUM('pending','sent','error','cancelled')");
    }
}
