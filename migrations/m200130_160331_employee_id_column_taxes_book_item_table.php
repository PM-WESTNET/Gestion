<?php

use yii\db\Migration;

/**
 * Class m200130_160331_employee_id_column_taxes_book_item_table
 */
class m200130_160331_employee_id_column_taxes_book_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('taxes_book_item', 'employee_bill_id', 'INT(11) NULL');
        $this->addForeignKey('fk_taxes_book_item_employee_bill', 'taxes_book_item', 'employee_bill_id', 'employee_bill','employee_bill_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('taxes_book_item', 'employee_bill_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200130_160331_employee_id_column_taxes_book_item_table cannot be reverted.\n";

        return false;
    }
    */
}
