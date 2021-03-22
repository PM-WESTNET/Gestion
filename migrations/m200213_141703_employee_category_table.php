<?php

use yii\db\Migration;

/**
 * Class m200213_141703_employee_category_table
 */
class m200213_141703_employee_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('employee_category', [
            'employee_category_id' => $this->primaryKey(),
            'name' => $this->string(45)->notNull(),
            'status' => 'ENUM("enabled","disabled") NOT NULL'
        ]);

        $this->addColumn('employee', 'employee_category_id', 'INT NULL');
        $this->addForeignKey('fk_employee_employee_category', 'employee', 'employee_category_id', 'employee_category', 'employee_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('employee', 'employee_category_id');
        $this->dropTable('employee_category');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_141703_employee_category_table cannot be reverted.\n";

        return false;
    }
    */
}
