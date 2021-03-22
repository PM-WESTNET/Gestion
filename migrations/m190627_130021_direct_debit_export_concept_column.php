<?php

use yii\db\Migration;

/**
 * Class m190627_130021_direct_debit_export_concept_column
 */
class m190627_130021_direct_debit_export_concept_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('direct_debit_export', 'concept', 'VARCHAR(22) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('direct_debit_export', 'concept');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_130021_direct_debit_export_concept_column cannot be reverted.\n";

        return false;
    }
    */
}
