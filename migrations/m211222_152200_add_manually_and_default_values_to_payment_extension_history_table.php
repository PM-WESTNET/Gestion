<?php

use yii\db\Migration;

/**
 * Class m211222_152200_add_manually_and_default_values_to_payment_extension_history_table
 */
class m211222_152200_add_manually_and_default_values_to_payment_extension_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = "ALTER TABLE payment_extension_history MODIFY payment_extension_history.from ENUM('app','ivr','manually','default') default 'default'";
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211222_152200_add_manually_and_default_values_to_payment_extension_history_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211222_152200_add_manually_and_default_values_to_payment_extension_history_table cannot be reverted.\n";

        return false;
    }
    */
}
