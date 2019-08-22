<?php

use yii\db\Migration;

/**
 * Class m190805_210346_old_value__log_table_to_text
 */
class m190805_210346_old_value__log_table_to_text extends Migration
{
    public function init()
    {
        $this->db= 'dblog';
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('log', 'old_value', 'TEXT NOT NULL');
        $this->alterColumn('log', 'new_value', 'TEXT NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('log', 'old_value', 'VARCHAR(255) NOT NULL');
        $this->alterColumn('log', 'new_value', 'VARCHAR(255) NOT NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_210346_old_value__log_table_to_text cannot be reverted.\n";

        return false;
    }
    */
}