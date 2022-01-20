<?php

use yii\db\Migration;

/**
 * Class m220120_195453_add_column_ip_4_old_repeats_on_connection_id_to_connection_table
 */
class m220120_195453_add_column_ip_4_old_repeats_on_connection_id_to_connection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%connection}}','ip_4_old_repeats_on_connection_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%connection}}','ip_4_old_repeats_on_connection_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220120_195453_add_column_ip_4_old_repeats_on_connection_id_to_connection_table cannot be reverted.\n";

        return false;
    }
    */
}
