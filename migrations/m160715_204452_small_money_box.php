<?php

use yii\db\Migration;

class m160715_204452_small_money_box extends Migration
{
    public function up()
    {
        $table = Yii::$app->db->schema->getTableSchema('money_box_account');
        
        if(!isset($table->columns['small_box'])) {
            $this->addColumn('money_box_account', 'small_box', 'boolean');
        }
        
        if(!isset($table->columns['small_box_last_closing_date'])) {
            $this->addColumn('money_box_account', 'small_box_last_closing_date', 'date');
        }
        
        if(!isset($table->columns['small_box_last_closing_time'])) {
            $this->addColumn('money_box_account', 'small_box_last_closing_time', 'time');
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
