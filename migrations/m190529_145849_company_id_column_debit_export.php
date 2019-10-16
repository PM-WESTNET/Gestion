<?php

use yii\db\Migration;

/**
 * Class m190529_145849_company_id_column_debit_export
 */
class m190529_145849_company_id_column_debit_export extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('direct_debit_export', 'company_id', 'INT(11) NOT NULL');
        $this->addForeignKey('fk_direct_debit_export_company', 'direct_debit_export', 'company_id', 'company', 'company_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('direct_debit_export', 'company_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190529_145849_company_id_column_debit_export cannot be reverted.\n";

        return false;
    }
    */
}
