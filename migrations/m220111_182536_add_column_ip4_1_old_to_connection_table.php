<?php

use yii\db\Migration;

/**
 * Class m220111_182536_add_column_ip4_1_old_to_connection_table
 */
class m220111_182536_add_column_ip4_1_old_to_connection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%connection}}','ip4_1_old', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220111_182536_add_column_ip4_1_old_to_connection_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220111_182536_add_column_ip4_1_old_to_connection_table cannot be reverted.\n";

        return false;
    }
    */
}
