<?php

use yii\db\Migration;

/**
 * Class m180711_171515_customer_needs_bill
 */
class m180711_171515_customer_needs_bill extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE customer ADD needs_bill tinyint(1) DEFAULT 0 NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180711_171515_customer_needs_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180711_171515_customer_needs_bill cannot be reverted.\n";

        return false;
    }
    */
}
