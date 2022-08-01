<?php

use yii\db\Migration;

/**
 * Handles adding timestamp to table `contract_detail_log`.
 */
class m220801_151127_add_timestamp_column_to_contract_detail_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contract_detail_log', 'created_at', $this->timestamp()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('contract_detail_log', 'created_at');
    }
    
}
