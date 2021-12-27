<?php

use yii\db\Migration;

/**
 * Class m211227_134657_add_column_previous_state_to_siro_payment_intention_table
 */
class m211227_134657_add_column_previous_state_to_siro_payment_intention_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /* $query = "ALTER TABLE bill MODIFY status ENUM('pending','canceled','payed') default 'draft'";
        $this->execute($query); */
        $this->addColumn('{{%siro_payment_intention}}','previous_state', "ENUM('pending','canceled','payed')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%siro_payment_intention}}','previous_state');

        /* echo "m211227_134657_add_column_previous_state_to_siro_payment_intention_table cannot be reverted.\n";

        return false; */
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211227_134657_add_column_previous_state_to_siro_payment_intention_table cannot be reverted.\n";

        return false;
    }
    */
}
