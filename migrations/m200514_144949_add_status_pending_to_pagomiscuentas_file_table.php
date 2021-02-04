<?php

use yii\db\Migration;

/**
 * Class m200514_144949_add_status_pending_to_pagomiscuentas_file_table
 */
class m200514_144949_add_status_pending_to_pagomiscuentas_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('pagomiscuentas_file', 'status', "ENUM('draft', 'pending', 'closed')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('pagomiscuentas_file', 'status', "ENUM('draft', 'pending', 'closed')");
    }
}
