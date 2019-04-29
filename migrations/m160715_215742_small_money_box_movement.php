<?php

use yii\db\Migration;

class m160715_215742_small_money_box_movement extends Migration
{
    public function up()
    {
        
        $table = Yii::$app->db->schema->getTableSchema('account_movement');
        
        if(!isset($table->columns['small_money_box_account_id'])) {
            $this->addColumn('account_movement', 'small_money_box_account_id', 'integer');
            $this->addForeignKey('fk_account_movement_id', 'account_movement', 'small_money_box_account_id', 'money_box_account', 'money_box_account_id');
        }
    }

    public function down()
    {
        echo "m160715_204452_small_money_box cannot be reverted.\n";

        return false;
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
