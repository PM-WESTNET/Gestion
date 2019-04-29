<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_202305_config_create_item_table extends Migration
{
    
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }
    
    public function up()
    {
        
        $this->createTable('item', [
            'item_id' => $this->primaryKey(),
            'attr' => $this->string(45)->notNull()->unique(),
            'type' => $this->string(45),
            'default' => $this->string(255),
            'label' => $this->string(140),
            'description' => $this->string(255),
            'multiple' => $this->boolean(),
            'category_id' => $this->integer()->notNull(),
            'superadmin' => $this->boolean()
        ]);
        
        $this->addForeignKey('fk_item_category1', 'item', 'category_id', 'category', 'category_id');
        
    }

    public function down()
    {
        $this->dropTable('item');
        
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
