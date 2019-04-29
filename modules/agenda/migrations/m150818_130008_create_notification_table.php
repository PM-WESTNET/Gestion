<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130008_create_notification_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //Notification
        if (\Yii::$app->dbagenda->schema->getTableSchema('notification') === null) {
            $this->createTable('notification', [
                //PKs
                'notification_id' => $this->primaryKey(),
                //FKs
                'user_id' => $this->integer()->notNull(),
                'task_id' => $this->integer()->notNull(),
                //Attrs
                'status' => $this->string(100),
                'datetime' => $this->integer(),
                'reason' => $this->text(),
                'show' => $this->boolean()->notNull()->defaultValue(true),
                'is_expired_reminder' => $this->boolean()->notNull()->defaultValue(false),
                    ], $tableOptions);
            $this->addForeignKey('fk_task_notification', 'notification', 'task_id', 'task', 'task_id');
        } else {
            $this->truncateTable('notification');
        }
    }

    public function safeDown() {
        
    }

}
