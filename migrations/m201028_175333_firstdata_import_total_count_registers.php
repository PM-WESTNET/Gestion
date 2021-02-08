<?php

use yii\db\Migration;

/**
 * Class m201028_175333_firstdata_import_total_count_registers
 */
class m201028_175333_firstdata_import_total_count_registers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_import', 'total', 'DOUBLE NULL');
        $this->addColumn('firstdata_import', 'registers', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_import', 'total');
        $this->dropColumn('firstdata_import', 'registers');
    }

}
