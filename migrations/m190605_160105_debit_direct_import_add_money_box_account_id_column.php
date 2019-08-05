<?php

use yii\db\Migration;

/**
 * Class m190605_160105_debit_direct_import_add_money_box_account_id_column
 */
class m190605_160105_debit_direct_import_add_money_box_account_id_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('debit_direct_import', 'money_box_account_id', 'INT(11) NOT NULL');

        $this->addForeignKey('fk_debit_direct_import_money_box_account', 'debit_direct_import', 'money_box_account_id', 'money_box_account', 'money_box_account_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('debit_direct_import', 'money_box_account_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190605_160105_debit_direct_import_add_money_box_account_id_column cannot be reverted.\n";

        return false;
    }
    */
}
