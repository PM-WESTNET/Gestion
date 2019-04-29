<?php

use yii\db\Migration;

/**
 * Class m190226_181807_from_date_pagomiscuentas_file_table
 */
class m190226_181807_from_date_pagomiscuentas_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('pagomiscuentas_file', 'from_date', 'date NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('pagomiscuentas_file', 'from_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190226_181807_from_date_pagomiscuentas_file_table cannot be reverted.\n";

        return false;
    }
    */
}
