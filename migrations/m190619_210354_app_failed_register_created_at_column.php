<?php

use yii\db\Migration;

/**
 * Class m190619_210354_app_failed_register_created_at_column
 */
class m190619_210354_app_failed_register_created_at_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('app_failed_register', 'created_at', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('app_failed_register', 'created_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190619_210354_app_failed_register_created_at_column cannot be reverted.\n";

        return false;
    }
    */
}
