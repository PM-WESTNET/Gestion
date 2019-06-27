<?php

use yii\db\Migration;

/**
 * Class m190507_210137_email_status
 */
class m190507_210137_email_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'email_status', 'VARCHAR(45) NULL DEFAULT "invalid"');
        $this->addColumn('customer', 'email2_status', 'VARCHAR(45) NULL DEFAULT "invalid"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer', 'email_status');
        $this->dropColumn('customer', 'email2_status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190507_210137_email_status cannot be reverted.\n";

        return false;
    }
    */
}
