<?php

use yii\db\Migration;

/**
 * Class m201229_183433_ip_range_subnet_status
 */
class m201229_183433_ip_range_subnet_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('ip_range', 'type', 'enum("node_subnet", "net", "subnet") NULL DEFAULT "node_subnet"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('ip_range', 'type', 'enum("node_subnet", "net") NULL DEFAULT "node_subnet"');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201229_183433_ip_range_subnet_status cannot be reverted.\n";

        return false;
    }
    */
}
