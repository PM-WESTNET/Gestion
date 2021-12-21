<?php

use yii\db\Migration;

/**
 * Class m211221_173032_add_column_payment_id_to_payment_intentions_accountability
 */
class m211221_173032_add_column_payment_id_to_payment_intentions_accountability extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_intentions_accountability}}', 'payment_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_intentions_accountability}}','payment_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_173032_add_column_payment_id_to_payment_intentions_accountability cannot be reverted.\n";

        return false;
    }
    */
}
