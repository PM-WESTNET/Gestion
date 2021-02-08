<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_130006_create_user_group_has_user_table extends Migration {

    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    public function safeUp() {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //User group has user Table
        if (\Yii::$app->dbagenda->schema->getTableSchema('user_group_has_user') === null) {
            $this->createTable('user_group_has_user', [
                //PKs
                'user_id' => $this->integer()->notNull(),
                'group_id' => $this->integer()->notNull(),
                    ], $tableOptions);
            $this->addForeignKey('fk_user_group', 'user_group_has_user', 'group_id', 'user_group', 'group_id');
        } else {
            $this->truncateTable('user_group_has_user');
        }
    }

    public function safeDown() {
        
    }

}
