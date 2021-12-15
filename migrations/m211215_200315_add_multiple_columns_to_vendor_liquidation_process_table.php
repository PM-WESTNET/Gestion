<?php

use yii\db\Migration;

/**
 * Handles adding multiple to table `vendor_liquidation_process`.
 */
class m211215_200315_add_multiple_columns_to_vendor_liquidation_process_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%vendor_liquidation_process}}','start_time', $this->datetime());
        $this->addColumn('{{%vendor_liquidation_process}}','finish_time', $this->datetime());
        $this->addColumn('{{%vendor_liquidation_process}}','time_spent', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%vendor_liquidation_process}}','start_time');
        $this->dropColumn('{{%vendor_liquidation_process}}','finish_time');
        $this->dropColumn('{{%vendor_liquidation_process}}','time_spent');
    }
}
