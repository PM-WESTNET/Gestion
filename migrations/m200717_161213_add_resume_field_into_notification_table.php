<?php

use yii\db\Migration;

/**
 * Class m200717_161213_add_resume_field_into_notification_table
 */
class m200717_161213_add_resume_field_into_notification_table extends Migration
{
    public function init()
    {
        $this->db = 'dbnotifications';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notification', 'resume', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('notification', 'resume');
    }
}
