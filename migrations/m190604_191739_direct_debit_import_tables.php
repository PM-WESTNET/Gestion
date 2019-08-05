<?php

use yii\db\Migration;

/**
 * Class m190604_191739_direct_debit_import_tables
 */
class m190604_191739_direct_debit_import_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('debit_direct_import', [
            'debit_direct_import_id' => $this->primaryKey(),
            'file' => $this->string(255)->null(),
            'import_timestamp' => $this->integer(),
            'process_timestamp' => $this->integer(),
            'status' => $this->integer(),
            'company_id' => $this->integer(11)->notNull(),
            'bank_id' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_debit_direct_import_bank', 'debit_direct_import', 'bank_id', 'bank', 'bank_id');
        $this->addForeignKey('fk_debit_direct_import_company', 'debit_direct_import', 'company_id', 'company', 'company_id');

        $this->createTable('debit_direct_import_has_payment', [
            'debit_direct_import_has_payment' => $this->primaryKey(),
            'payment_id' => $this->integer(11)->notNull(),
            'debit_direct_import_id' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_debit_direct_import_payment_payment', 'debit_direct_import_has_payment', 'payment_id', 'payment', 'payment_id');
        $this->addForeignKey('fk_debit_direct_import_payment_import', 'debit_direct_import_has_payment', 'debit_direct_import_id', 'debit_direct_import', 'debit_direct_import_id');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('debit_direct_import_has_payment');
       $this->dropTable('debit_direct_import');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190604_191739_direct_debit_import_tables cannot be reverted.\n";

        return false;
    }
    */
}
