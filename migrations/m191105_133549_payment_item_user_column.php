<?php

use yii\db\Migration;

/**
 * Class m191105_133549_payment_item_user_column
 */
class m191105_133549_payment_item_user_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_item', 'user_id','INT NULL');
        $this->addForeignKey('fk_payment_item_user', 'payment_item', 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_payment_item_user', 'payment_item');
        $this->dropColumn('payment_item', 'user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191105_133549_payment_item_user_column cannot be reverted.\n";

        return false;
    }
    */
}
