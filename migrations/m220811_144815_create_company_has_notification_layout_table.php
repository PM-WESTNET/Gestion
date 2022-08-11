<?php

use yii\db\Migration;
use app\components\helpers\DbHelper;
/**
 * Handles the creation of table `company_has_notification_layout`.
 */
class m220811_144815_create_company_has_notification_layout_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $dbgestion = DbHelper::getDbName(Yii::$app->db);
        $dbnotifications = DbHelper::getDbName(Yii::$app->dbnotifications);
        $this->createTable($dbnotifications.'.company_has_notification_layout', [
            'id' => $this->primaryKey(),
            'layout_path' => $this->string(255)->notNull(),
            'company_id' => $this->integer()->notNull(),
            'is_enabled' => $this->boolean()->defaultValue(TRUE),
        ]);

        //add foreign key for table `user`
        $this->addForeignKey(
            'company_has_notification_layout_company_id_fk',
            $dbnotifications.'.company_has_notification_layout',
            'company_id',
            $dbgestion.'.company',
            'company_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(DbHelper::getDbName(Yii::$app->dbnotifications).'.company_has_notification_layout');
    }


    
}
