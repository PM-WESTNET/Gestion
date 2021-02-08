<?php

use yii\db\Migration;

/**
 * Class m210208_140529_notice_hour_column_customer_class
 */
class m210208_140529_notice_hour_column_customer_class extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_class', 'notice_hour', 'TIME NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer_class', 'notice_hour');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_140529_notice_hour_column_customer_class cannot be reverted.\n";

        return false;
    }
    */
}
