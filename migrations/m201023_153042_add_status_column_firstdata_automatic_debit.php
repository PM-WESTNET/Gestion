<?php

use yii\db\Migration;

/**
 * Class m201023_153042_add_status_column_firstdata_automatic_debit
 */
class m201023_153042_add_status_column_firstdata_automatic_debit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_automatic_debit', 'status', 'ENUM("enabled","disabled") NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_automatic_debit', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_153042_add_status_column_firstdata_automatic_debit cannot be reverted.\n";

        return false;
    }
    */
}
