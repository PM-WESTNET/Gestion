<?php

use yii\db\Migration;

/**
 * Class m210118_121539_firstdata_automatic_debit_user
 */
class m210118_121539_firstdata_automatic_debit_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_automatic_debit', 'user_id', 'INT NULL');
        $this->addForeignKey('firstdata_automatic_debit_user_fk', 'firstdata_automatic_debit', 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_automatic_debit', 'user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210118_121539_firstdata_automatic_debit_user cannot be reverted.\n";

        return false;
    }
    */
}
