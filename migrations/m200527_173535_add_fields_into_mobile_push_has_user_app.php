<?php

use yii\db\Migration;

/**
 * Class m200527_173535_add_fields_into_mobile_push_has_user_app
 */
class m200527_173535_add_fields_into_mobile_push_has_user_app extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mobile_push_has_user_app', 'customer_id', $this->integer()->defaultValue(null));
        $this->addColumn('mobile_push_has_user_app', 'notification_title', $this->text()->defaultValue(null));
        $this->addColumn('mobile_push_has_user_app', 'notification_content', $this->text()->defaultValue(null));
        $this->addColumn('mobile_push_has_user_app', 'notification_read', $this->boolean()->defaultValue(0));

        $this->addForeignKey('fk_mobile_push_has_user_app_customer_id', 'mobile_push_has_user_app', 'customer_id', 'customer', 'customer_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_mobile_push_has_user_app_customer_id', 'mobile_push_has_user_app');

        $this->dropColumn('mobile_push_has_user_app', 'notification_read');
        $this->dropColumn('mobile_push_has_user_app', 'notification_content');
        $this->dropColumn('mobile_push_has_user_app', 'notification_title');
        $this->dropColumn('mobile_push_has_user_app', 'customer_id');

    }
}
