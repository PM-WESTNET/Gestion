<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130003_create_category_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //Category Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('category') === null) {
            $this->createTable('category', [
                //PKs
                'category_id' => $this->primaryKey(),
                //Attrs
                'name' => $this->string(255)->notNull(),
                'description' => $this->text(),
                'default_duration' => $this->time()->notNull(),
                'slug' => $this->string(45)->notNull()
                    ], $tableOptions);
        } else {
            $this->truncateTable('category');
        }
    }

    public function safeDown() {
        
    }

}
