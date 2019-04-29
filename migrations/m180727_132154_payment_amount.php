<?php

use yii\db\Migration;

/**
 * Class m180727_132154_payment_amount
 */
class m180727_132154_payment_amount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE payment MODIFY amount double NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180727_132154_payment_amount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180727_132154_payment_amount cannot be reverted.\n";

        return false;
    }
    */
}
