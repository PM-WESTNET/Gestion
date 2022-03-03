<?php

use yii\db\Migration;

/**
 * Class m220302_201834_add_column_server_ip_to_server_table
 */
class m220302_201834_add_column_server_ip_to_server_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%server}}','load_balancer_type', "ENUM('Wispro','Mikrotik')"); // enum for the different types of load balancers
        $this->addColumn('{{%server}}','ip_of_load_balancer', $this->text()); // INET_NTOA() to retrieve as normal IP. ip2long() and long2ip() in PHP to manipulate the data
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220302_201834_add_column_server_ip_to_server_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220302_201834_add_column_server_ip_to_server_table cannot be reverted.\n";

        return false;
    }
    */
}
