<?php

use yii\db\Migration;

class m181018_085614_add_notification_id_into_integratech_message_table extends Migration
{
    public function init(){
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('integratech_message', 'notification_id', $this->integer());
        $this->addForeignKey('fk_integratech_message', 'integratech_message', 'notification_id', 'notification', 'notification_id');
        $this->addColumn('integratech_message', 'customer_id', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn('integratech_message', 'customer_id');
        $this->dropForeignKey('fk_integratech_message');
        $this->dropColumn('integratech_message', 'notification_id');
    }
}
