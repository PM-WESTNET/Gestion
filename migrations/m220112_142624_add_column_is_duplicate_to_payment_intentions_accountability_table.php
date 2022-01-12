<?php

use yii\db\Migration;

/**
 * Class m220112_142624_add_column_is_duplicate_to_payment_intentions_accountability_table
 */
class m220112_142624_add_column_is_duplicate_to_payment_intentions_accountability_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_intentions_accountability}}','is_duplicate', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220112_142624_add_column_is_duplicate_to_payment_intentions_accountability_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_142624_add_column_is_duplicate_to_payment_intentions_accountability_table cannot be reverted.\n";

        return false;
    }
    */
}
