<?php

use yii\db\Migration;

/**
 * Class m200909_201514_notification_error_message
 */
class m200909_201514_notification_error_message extends Migration
{
    public function init() {
        $this->db = 'dbnotifications';
        parent::init();
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notification', 'error_msg', 'TEXT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('notification', 'error_msg');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200909_201514_notification_error_message cannot be reverted.\n";

        return false;
    }
    */
}
