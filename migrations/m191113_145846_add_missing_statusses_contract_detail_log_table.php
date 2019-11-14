<?php

use yii\db\Migration;

/**
 * Class m191113_145846_add_missing_statusses_contract_detail_log_table
 */
class m191113_145846_add_missing_statusses_contract_detail_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('contract_detail_log', 'status', 'enum("draft","active","canceled","low","low-process") NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('contract_detail_log', 'status', 'enum("draft","active","canceled") NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191113_145846_add_missing_statusses_contract_detail_log_table cannot be reverted.\n";

        return false;
    }
    */
}
