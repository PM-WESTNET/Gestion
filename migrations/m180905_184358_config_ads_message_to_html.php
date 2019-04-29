<?php

use yii\db\Migration;

/**
 * Class m180905_184358_config_ads_message_to_html
 */
class m180905_184358_config_ads_message_to_html extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("UPDATE `item` SET `type` = 'html' WHERE `attr` = 'ads-message';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }


}
