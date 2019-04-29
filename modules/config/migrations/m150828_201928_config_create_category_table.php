<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_201928_config_create_category_table extends Migration
{
    
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        
        $this->createTable('category', [
            'category_id' => $this->primaryKey(),
            'name' => $this->string(45)->notNull(),
            'status' => 'enum("enabled","disabled")',
            'superadmin' => $this->boolean()
        ]);
        
    }

    public function down()
    {
        
        $this->dropTable('category');
        
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
