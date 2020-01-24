<?php

use yii\db\Migration;

/**
 * Class m200124_132607_status_column_account_table
 */
class m200124_132607_status_column_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'status', 'ENUM("enabled","disabled") NOT NULL DEFAULT "enabled"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200124_132607_status_column_account_table cannot be reverted.\n";

        return false;
    }
    */
}
