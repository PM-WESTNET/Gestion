<?php

use yii\db\Migration;

/**
 * Class m191024_215545_post_column_to_long_text_log_table
 */
class m191024_215545_post_column_to_long_text_log_table extends Migration
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
        $this->alterColumn('log', 'post', 'LONGTEXT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('log', 'post', 'TEXT NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191024_215545_post_column_to_long_text_log_table cannot be reverted.\n";

        return false;
    }
    */
}
