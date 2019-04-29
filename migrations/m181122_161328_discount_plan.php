<?php

use yii\db\Migration;

/**
 * Class m181122_161328_discount_plan
 */
class m181122_161328_discount_plan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE discount MODIFY value_from enum('total', 'product', 'plan');");
        $this->execute("ALTER TABLE discount ADD COLUMN referenced INT(11) NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181122_161328_discount_plan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181122_161328_discount_plan cannot be reverted.\n";

        return false;
    }
    */
}
