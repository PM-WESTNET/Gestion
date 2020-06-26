<?php

use yii\db\Migration;

/**
 * Class m200626_201414_change_charset_on_tables_for_emojis
 */
class m200626_201414_change_charset_on_tables_for_emojis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db_notifications = \app\components\helpers\DbHelper::getDbName(Yii::$app->dbnotifications);

        Yii::$app->db->createCommand("ALTER TABLE $db_notifications.notifications CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
        Yii::$app->db->createCommand("ALTER TABLE mobile_push CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
        Yii::$app->db->createCommand("ALTER TABLE mobile_push_has_user_app CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
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
