<?php

use yii\db\Migration;

/**
 * Class m200114_160537_created_at_customer_has_discount
 */
class m200114_160537_created_at_customer_has_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_has_discount', 'created_at', 'INT NULL');
        $this->addColumn('customer_has_discount', 'updated_at', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer_has_discount', 'created_at');
        $this->dropColumn('customer_has_discount', 'updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200114_160537_created_at_customer_has_discount cannot be reverted.\n";

        return false;
    }
    */
}
