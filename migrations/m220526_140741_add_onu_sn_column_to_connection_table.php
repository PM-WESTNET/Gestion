<?php

use yii\db\Migration;

/**
 * Handles adding onu_sn to table `connection`.
 */
class m220526_140741_add_onu_sn_column_to_connection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('connection', 'onu_sn', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('connection', 'onu_sn');
    }
}
