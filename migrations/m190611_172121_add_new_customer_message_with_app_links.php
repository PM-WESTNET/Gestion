<?php

use yii\db\Migration;

class m190611_172121_add_new_customer_message_with_app_links extends Migration
{

    public function safeUp()
    {
        $this->insert('customer_message', [
            'name' => 'Links de aplicación móvil',
            'message' => 'Estimado {customer_name} lo invitamos a que descargue la aplicación desde el siguiente link https://play.google.com/store/apps/details?id=ar.com.westnet.customer.app. Atentamente Westnet',
            'status' => 10
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('customer_message', ['name' => 'Links de aplicación móvil']);
    }
}
