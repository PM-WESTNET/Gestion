<?php

use yii\db\Migration;

/**
 * Class m200609_121010_add_timestamp_fields_into_mobile_push_has_user_app_table
 */
class m200609_121010_add_timestamp_fields_into_mobile_push_has_user_app_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mobile_push_has_user_app', 'created_at', $this->integer());
        $this->addColumn('mobile_push_has_user_app', 'sent_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('mobile_push_has_user_app', 'sent_at');
        $this->dropColumn('mobile_push_has_user_app', 'created_at');
    }
}
