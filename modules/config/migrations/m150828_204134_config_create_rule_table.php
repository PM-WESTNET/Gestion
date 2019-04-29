<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_204134_config_create_rule_table extends Migration
{
    
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {

        $this->createTable('rule', [
            'rule_id' => $this->primaryKey(),
            'message' => $this->string(255),
            'max' => $this->double(),
            'min' => $this->double(),
            'pattern' => $this->string(255),
            'format' => $this->string(45),
            'targetAttribute' => $this->string(45),
            'targetClass' => $this->string(255),
            'item_id' => $this->integer()->notNull(),
            'validator' => $this->string(45)->notNull()
        ]);
        
        $this->addForeignKey('fk_rule_item1_idx', 'rule', 'item_id', 'item', 'item_id');
        
    }

    public function down()
    {
        $this->dropTable('rule');
        
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
