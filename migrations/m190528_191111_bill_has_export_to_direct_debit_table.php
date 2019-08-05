<?php

use yii\db\Migration;

/**
 * Class m190528_191111_bill_has_export_to_direct_debit_table
 */
class m190528_191111_bill_has_export_to_direct_debit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('direct_debit_export', [
            'direct_debit_export_id' => $this->primaryKey(),
            'file' => $this->string(255),
            'create_timestamp' => $this->integer(),
            'bank_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_direct_debit_export_bank', 'direct_debit_export', 'bank_id', 'bank', 'bank_id');

        $this->createTable('bill_has_export_to_debit', [
           'bill_has_export_to_debit' => $this->primaryKey(),
           'bill_id' => $this->integer(11),
           'direct_debit_export_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_bill_has_export_to_debit_bill', 'bill_has_export_to_debit', 'bill_id', 'bill', 'bill_id');
        $this->addForeignKey('fk_bill_has_export_to_debit_direct_debit_export', 'bill_has_export_to_debit', 'direct_debit_export_id', 'direct_debit_export', 'direct_debit_export_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bill_has_export_to_debit');
        $this->dropTable('direct_debit_export');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190528_191111_bill_has_export_to_direct_debit_table cannot be reverted.\n";

        return false;
    }
    */
}
