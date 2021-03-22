<?php

use yii\db\Migration;

/**
 * Class m191227_171438_add_birthdate_column_customer
 */
class m191227_171438_add_birthdate_column_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'birthdate', 'DATE NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer', 'birthdate');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191227_171438_add_birthdate_column_customer cannot be reverted.\n";

        return false;
    }
    */
}
