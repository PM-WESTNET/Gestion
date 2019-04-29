<?php

use yii\db\Migration;

class m181012_142412_add_integratech_messages_table extends Migration
{
    public function init(){
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('integratech_message',[
                'integratech_message_id' => $this->primaryKey(),
                'message' => $this->text(300),
                'phone' => $this->text(45),
                'datetime' => $this->timestamp()->defaultValue(null),
                'status' => "ENUM('pending', 'sent', 'error', 'cancelled')",
                'response_message_id' => $this->text(),
                'response_status_code' => $this->integer(),
                'response_status_text' => $this->text(),
            ]);

        $this->insert('transport', [
                'name' => 'SMS Integratech',
                'slug' => 'sms-integratech',
                'description' => 'Envia una notificación SMS a los destinatarios utilizando Integratech',
                'class' => 'app\modules\westnet\notifications\components\transports\SMSIntegratechTransport',
                'status' => 'enabled'
            ]);
    }

    public function safeDown()
    {
        $this->delete('transport',['slug' => 'sms-integratech']);
        $this->dropTable('integratech_message');
    }

}
