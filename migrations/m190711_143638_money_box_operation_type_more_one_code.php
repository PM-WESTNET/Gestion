<?php

use yii\db\Migration;

/**
 * Class m190711_143638_money_box_operation_type_more_one_code
 */
class m190711_143638_money_box_operation_type_more_one_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('ix_money_box_id_operation_type_id_UNIQUE', 'money_box_has_operation_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('ix_money_box_id_operation_type_id_UNIQUE', 'money_box_has_operation_type', ['operation_type_id', 'money_box_id', 'account_id'], true);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190711_143638_money_box_operation_type_more_one_code cannot be reverted.\n";

        return false;
    }
    */
}
