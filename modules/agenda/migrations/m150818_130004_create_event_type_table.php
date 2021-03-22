<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130004_create_event_type_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //Event type Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('event_type') === null) {
            $this->createTable('event_type', [
                //PKs
                'event_type_id' => $this->primaryKey(),
                //Attrs
                'name' => $this->string(255)->notNull(),
                'description' => $this->text(),
                'slug' => $this->string(45)->notNull()
                    ], $tableOptions);
        } else {
            $this->truncateTable('event_type');
        }
    }

    public function safeDown() {
        
    }

}
