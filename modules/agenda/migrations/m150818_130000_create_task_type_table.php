<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130000_create_task_type_table extends Migration {
    
    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {        
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //TaskType Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('task_type') === null) {
            $this->createTable('task_type', [
                //PKs
                'task_type_id' => $this->primaryKey(),
                //Attrs
                'name' => $this->string(255)->notNull(),
                'description' => $this->text(),
                'slug' => $this->string(45)->notNull()
            ],$tableOptions);
        }else{
            $this->truncateTable('task_type');
        }
        
    }

    public function safeDown() {
        
    }

}
