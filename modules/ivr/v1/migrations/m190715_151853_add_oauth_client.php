<?php

use yii\db\Migration;

/**
 * Class m190715_151853_add_oauth_client
 */
class m190715_151853_add_oauth_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('oauth2_client', [
            'client_id' => 'ivr_user',
            'client_secret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe',
            'redirect_uri' => 'com.ivr.v1', //Cambiar esto por nombre de la api, generalmente es la url invevertidada
            'created_at' => time(),
            'updated_at' => time(),
            'created_by' => 1,
            'updated_by' => 1
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_151853_add_oauth_client cannot be reverted.\n";

        return false;
    }
    */
}
