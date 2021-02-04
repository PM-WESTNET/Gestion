<?php

use yii\db\Migration;

/**
 * Class m200213_155527_init_date_finish_date_and_observations_columns_employee_table
 */
class m200213_155527_init_date_finish_date_and_observations_columns_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('employee','init_date', 'INT NULL');
        $this->addColumn('employee','finish_date', 'INT NULL');
        $this->addColumn('employee','observations', 'TEXT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('employee','init_date');
        $this->dropColumn('employee','finish_date');
        $this->dropColumn('employee','observations');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_155527_init_date_finish_date_and_observations_columns_employee_table cannot be reverted.\n";

        return false;
    }
    */
}
