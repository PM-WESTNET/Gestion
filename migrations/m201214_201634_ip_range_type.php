<?php

use yii\db\Migration;

/**
 * Class m201214_201634_ip_range_type
 */
class m201214_201634_ip_range_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_ip_range_access_point', 'ip_range');
        $this->dropColumn('ip_range', 'access_point_id');
        $this->addColumn('ip_range', 'type', 'enum("node_subnet", "net") NULL DEFAULT "node_subnet"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ip_range', 'type');
        $this->addColumn('ip_range', 'access_point_id', 'INT NULL');
        $this->addForeignKey('fk_ip_range_access_point', 'ip_range', 'access_point_id', 'access_point', 'access_point_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_201634_ip_range_type cannot be reverted.\n";

        return false;
    }
    */
}
