<?php

use yii\db\Migration;

/**
 * Class m200128_173858_fix_bithday_column_name_employee_table
 */
class m200128_173858_fix_bithday_column_name_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('employee', 'birthdday', 'birthday');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('employee', 'brithday', 'birthdday DATE NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200128_173858_fix_bithday_column_name_employee_table cannot be reverted.\n";

        return false;
    }
    */
}
