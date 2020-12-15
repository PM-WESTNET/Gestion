<?php

use yii\db\Migration;

/**
 * Class m201215_150559_last_ip_column
 */
class m201215_150559_last_ip_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ip_range', 'last_ip', 'INT(11) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ip_range', 'last_ip');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201215_150559_last_ip_column cannot be reverted.\n";

        return false;
    }
    */
}
