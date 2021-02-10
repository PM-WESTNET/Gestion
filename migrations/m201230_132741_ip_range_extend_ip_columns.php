<?php

use yii\db\Migration;

/**
 * Class m201230_132741_ip_range_extend_ip_columns
 */
class m201230_132741_ip_range_extend_ip_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('ip_range', 'ip_start', 'BIGINT NOT NULL');
        $this->alterColumn('ip_range', 'ip_end', 'BIGINT NOT NULL');
        $this->alterColumn('ip_range', 'last_ip', 'BIGINT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('ip_range', 'ip_start', 'INT(11) NOT NULL');
        $this->alterColumn('ip_range', 'ip_end', 'INT(11) NOT NULL');
        $this->alterColumn('ip_range', 'last_ip', 'INT(11) NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_132741_ip_range_extend_ip_columns cannot be reverted.\n";

        return false;
    }
    */
}
