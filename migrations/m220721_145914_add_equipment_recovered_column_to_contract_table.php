<?php

use yii\db\Migration;

/**
 * Handles adding equipment_recovered to table `contract`.
 */
class m220721_145914_add_equipment_recovered_column_to_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('contract', 'equipment_recovered', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('contract', 'equipment_recovered');
    }
}
