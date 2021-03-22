<?php

use yii\db\Migration;

/**
 * Class m200213_145716_insert_employee_categories
 */
class m200213_145716_insert_employee_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('employee_category', ['name' => 'MAESTRANZA A', 'status' => 'enabled']);
        $this->insert('employee_category', ['name' => 'ADMINISTRATIVO A', 'status' => 'enabled']);
        $this->insert('employee_category', ['name' => 'ADMINISTRATIVO B', 'status' => 'enabled']);
        $this->insert('employee_category', ['name' => 'AUXILIAR ESPECIALIZADO B', 'status' => 'enabled']);
        $this->insert('employee_category', ['name' => 'DIRECTOR', 'status' => 'enabled']);
        $this->insert('employee_category', ['name' => 'TECNICO', 'status' => 'enabled']);
        $this->insert('employee_category', ['name' => 'SIN CATEGORIA', 'status' => 'enabled']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('employee_category', ['name' => 'MAESTRANZA A']);
        $this->delete('employee_category', ['name' => 'ADMINISTRATIVO A']);
        $this->delete('employee_category', ['name' => 'ADMINISTRATIVO B']);
        $this->delete('employee_category', ['name' => 'AUXILIAR ESPECIALIZADO B']);
        $this->delete('employee_category', ['name' => 'DIRECTOR']);
        $this->delete('employee_category', ['name' => 'TECNICO']);
        $this->delete('employee_category', ['name' => 'SIN CATEGORIA']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_145716_insert_employee_categories cannot be reverted.\n";

        return false;
    }
    */
}
