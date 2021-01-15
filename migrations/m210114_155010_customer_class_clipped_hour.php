<?php

use yii\db\Migration;

/**
 * Class m210114_155010_customer_class_clipped_hour
 */
class m210114_155010_customer_class_clipped_hour extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer_class', 'clip_hour', 'TIME NULL');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer_class', 'clip_hour');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_155010_customer_class_clipped_hour cannot be reverted.\n";

        return false;
    }
    */
}
