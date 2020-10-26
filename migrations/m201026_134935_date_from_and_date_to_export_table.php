<?php

use yii\db\Migration;

/**
 * Class m201026_134935_date_from_and_date_to_export_table
 */
class m201026_134935_date_from_and_date_to_export_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_export', 'from_date', 'INT NOT NULL');
        $this->addColumn('firstdata_export', 'to_date', 'INT NULL');
        $this->addColumn('firstdata_export', 'status', 'ENUM("draft","exported")');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_export', 'from_date');
        $this->dropColumn('firstdata_export', 'to_date');
        $this->dropColumn('firstdata_export', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_134935_date_from_and_date_to_export_table cannot be reverted.\n";

        return false;
    }
    */
}
