<?php

use yii\db\Migration;

/**
 * Class m190902_153525_notify_payment_type
 */
class m190902_153525_notify_payment_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notify_payment', 'from', 'VARCHAR(45) NOT NULL DEFAULT "App"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('notify_payment', 'from');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190902_153525_notify_payment_type cannot be reverted.\n";

        return false;
    }
    */
}
