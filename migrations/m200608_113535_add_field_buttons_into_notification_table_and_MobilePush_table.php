<?php

use yii\db\Migration;

/**
 * Class m200608_113535_add_field_buttons_into_notification_table_and_MobilePush_table
 */
class m200608_113535_add_field_buttons_into_notification_table_and_MobilePush_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mobile_push', 'notification_id', $this->integer());
        $db_notifications = \app\components\helpers\DbHelper::getDbName(Yii::$app->dbnotifications);

        $this->addForeignKey('fk_mobile_push_notification_id', 'mobile_push', 'notification_id', $db_notifications.".notification", 'notification_id');

        $this->addColumn($db_notifications.'.notification', 'buttoms', $this->string());
        $this->addColumn('mobile_push', 'buttoms', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $db_notifications = \app\components\helpers\DbHelper::getDbName(Yii::$app->dbnotifications);

        $this->dropColumn('mobile_push', 'buttoms');
        $this->dropColumn($db_notifications.'.notification', 'buttoms');
        $this->dropForeignKey('fk_mobile_push_notification_id', 'mobile_push');
        $this->dropColumn('mobile_push', 'notification_id');
    }
}
