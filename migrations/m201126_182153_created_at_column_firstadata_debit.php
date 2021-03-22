<?php

use yii\db\Migration;

/**
 * Class m201126_182153_created_at_column_firstadata_debit
 */
class m201126_182153_created_at_column_firstadata_debit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_automatic_debit', 'created_at', 'INT NULL');
        $this->addColumn('firstdata_automatic_debit', 'updated_at', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_automatic_debit', 'created_at');
        $this->dropColumn('firstdata_automatic_debit', 'updated_at');
    }

    
}
