<?php

use yii\db\Migration;

/**
 * Class m201028_213422_firstdata_import_money_box_account_id
 */
class m201028_213422_firstdata_import_money_box_account_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_import', 'money_box_account_id', 'INTEGER NOT NULL');
        $this->addForeignKey('fk_firstdata_import_money_box_account', 'firstdata_import', 'money_box_account_id', 'money_box_account', 'money_box_account_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_import', 'money_box_account_id');
    }

}
