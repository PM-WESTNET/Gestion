<?php

use yii\db\Migration;

/**
 * Class m211221_170000_add_column_timestamps_to_payment_intentions_accountability
 */
class m211221_170000_add_column_timestamps_to_payment_intentions_accountability extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_intentions_accountability}}', 'created_at', $this->date());
        $this->addColumn('{{%payment_intentions_accountability}}', 'updated_at', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_intentions_accountability}}','created_at');
        $this->dropColumn('{{%payment_intentions_accountability}}','updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_170000_add_column_timestamps_to_payment_intentions_accountability cannot be reverted.\n";

        return false;
    }
    */
}
