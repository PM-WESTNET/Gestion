<?php

use yii\db\Migration;

/**
 * Class m220201_134405_add_column_had_error_to_bill_table
 */
class m220201_134405_add_column_had_error_to_bill_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%bill}}','had_error', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220201_134405_add_column_had_error_to_bill_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220201_134405_add_column_had_error_to_bill_table cannot be reverted.\n";

        return false;
    }
    */
}
