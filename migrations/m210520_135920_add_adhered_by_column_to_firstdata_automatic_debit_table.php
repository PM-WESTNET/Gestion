<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%firstdata_automatic_debit}}`.
 */
class m210520_135920_add_adhered_by_column_to_firstdata_automatic_debit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->addColumn('{{%firstdata_automatic_debit}}', 'adhered_by', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%firstdata_automatic_debit}}','adhered_by');
    }
}
