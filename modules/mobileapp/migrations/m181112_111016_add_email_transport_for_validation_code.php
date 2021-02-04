<?php

use yii\db\Migration;

class m181112_111016_add_email_transport_for_validation_code extends Migration
{

    public function safeUp()
    {
        $this->insert('email_transport',[
            'name' => 'CODIGO_VALIDACION_EN_APP',
            'from_email' => 'no-reply@notificaciones.westnet.com.ar',
            'transport' =>  'Swift_SmtpTransport',
            'host' => 'email-smtp.us-east-1.amazonaws.com',
            'port' => '587',
            'username' => 'AKIAIP55WY46PZFSLNNA',
            'password' => 'AhSMPPdEJO0Tcp/g9HcSvxMtyU1B5PI+VAQTRwVJA0iI',
            'encryption' => 'TLS',
            'layout' => '@app/modules/mobileapp/v1/views/mailing/validation-code',
            'relation_class' => 'app\modules\sale\models\Company',
            'relation_id' => 8
        ]);
    }

    public function safeDown()
    {
        $this->delete('email_transport', ['name' => 'CODIGO_VALIDACION_EN_APP']);
    }
}
