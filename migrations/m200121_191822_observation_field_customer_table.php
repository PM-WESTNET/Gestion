<?php

use yii\db\Migration;

/**
 * Class m200121_191822_observation_field_customer_table
 */
class m200121_191822_observation_field_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'observations', 'TEXT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer', 'observations');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200121_191822_observation_field_customer_table cannot be reverted.\n";

        return false;
    }
    */
}
