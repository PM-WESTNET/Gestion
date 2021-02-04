<?php

use yii\db\Migration;

class m181029_095812_add_datetime_into_integratech_received_messages extends Migration
{
    public function init(){
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('integratech_received_sms', 'datetime', $this->timestamp()->defaultValue(null));
    }
    public function safeDown()
    {
        $this->dropColumn('integratech_received_sms', 'datetime');
    }
}
