<?php

use yii\db\Migration;

/**
 * Class m190325_130224_code_column_money_box_has_operation_type
 */
class m190325_130224_code_column_money_box_has_operation_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('money_box_has_operation_type', 'code', 'VARCHAR(45) NULL');

        $this->alterColumn('money_box_has_operation_type', 'operation_type_id', 'INT(11) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('money_box_has_operation_type', 'operation_type_id', 'INT(11) NOT NULL');

        $this->dropColumn('money_box_has_operation_type', 'code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190325_130224_code_column_money_box_has_operation_type cannot be reverted.\n";

        return false;
    }
    */
}
