<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_203927_config_create_config_table extends Migration
{

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {

        $this->createTable('config', [
            'config_id' => $this->primaryKey(),
            'value' => $this->text(),
            'item_id' => $this->integer()->notNull(),
        ]);
        
        $this->addForeignKey('fk_config_item_idx', 'config', 'item_id', 'item', 'item_id');
        
        
    }

    public function down()
    {
        $this->dropTable('config');
        
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
