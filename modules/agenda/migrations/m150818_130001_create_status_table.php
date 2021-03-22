<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130001_create_status_table extends Migration {
    
    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        //Status Table        
        if (\Yii::$app->dbagenda->schema->getTableSchema('status') === null) {
            $this->createTable('status', [
                //PKs
                'status_id' => $this->primaryKey(),
                //Attrs
                'name' => $this->string(255)->notNull(),
                'description' => $this->text(),
                'color' => $this->string(100),
                'slug' => $this->string(45)->notNull()
            ], $tableOptions);
        }else{
            $this->truncateTable('status');
        }
        
    }

    public function safeDown() {
        
    }

}
