<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130005_create_user_group_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //User group Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('user_group') === null) {
            $this->createTable('user_group', [
                //PKs
                'group_id' => $this->primaryKey(),
                //Attrs
                'name' => $this->string(255)->notNull(),
                'descripion' => $this->text(),
                    ], $tableOptions);
        } else {
            $this->truncateTable('user_group');
        }
    }

    public function safeDown() {
        
    }

}
