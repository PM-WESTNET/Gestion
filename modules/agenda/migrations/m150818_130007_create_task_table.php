<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130007_create_task_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //Task Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('task') === null) {
            $this->createTable('task', [
                //PKs
                'task_id' => $this->primaryKey(),
                //FKs
                'task_type_id' => $this->integer()->notNull(),
                'status_id' => $this->integer()->notNull(),
                'creator_id' => $this->integer()->notNull(),
                'category_id' => $this->integer()->defaultValue(null),
                'parent_id' => $this->integer()->defaultValue(null),
                //Attrs
                'name' => $this->string(255)->notNull(),
                'description' => $this->text(),
                'date' => $this->date()->notNull(),
                'time' => $this->time()->notNull(),
                'datetime' => $this->integer(),
                'priority' => $this->integer()->notNull(),
                'duration' => $this->time()->notNull(),
                'slug' => $this->string(45)->notNull()
                    ], $tableOptions);
            $this->addForeignKey('fk_task_type', 'task', 'task_type_id', 'task_type', 'task_type_id');
            $this->addForeignKey('fk_status', 'task', 'status_id', 'status', 'status_id');
            $this->addForeignKey('fk_category', 'task', 'category_id', 'category', 'category_id');
            $this->addForeignKey('fk_task', 'task', 'parent_id', 'task', 'task_id');
            $this->createIndex('fk_task_type_idx', 'task', 'task_type_id');
            $this->createIndex('fk_status_idx', 'task', 'status_id');
            $this->createIndex('fk_category_idx', 'task', 'category_id');
            $this->createIndex('fk_task_idx', 'task', 'parent_id');
        } else {
            $this->truncateTable('task');
        }
    }

    public function safeDown() {
        
    }

}
