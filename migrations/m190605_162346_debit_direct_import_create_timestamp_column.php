<?php

use yii\db\Migration;

/**
 * Class m190605_162346_debit_direct_import_create_timestamp_column
 */
class m190605_162346_debit_direct_import_create_timestamp_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('debit_direct_import', 'create_timestamp', 'INT NOT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('debit_direct_import', 'create_timestamp');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190605_162346_debit_direct_import_create_timestamp_column cannot be reverted.\n";

        return false;
    }
    */
}
