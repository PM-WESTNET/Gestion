<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130009_create_event_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //Event Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('event') === null) {
            $this->createTable('event', [
                //PKs
                'event_id' => $this->primaryKey(),
                //FKs
                'task_id' => $this->integer()->notNull(),
                'event_type_id' => $this->integer()->notNull(),
                'user_id' => $this->integer()->notNull(),
                //Attrs
                'body' => $this->text(),
                'date' => $this->date(),
                'time' => $this->time(),
                'datetime' => $this->integer(),
                    ], $tableOptions);
            $this->addForeignKey('fk_task_event', 'event', 'task_id', 'task', 'task_id');
            $this->addForeignKey('fk_event_type', 'event', 'event_type_id', 'event_type', 'event_type_id');
        } else {
            $this->truncateTable('event');
        }
    }

    public function safeDown() {
        
    }

}
