<?php

use yii\db\Migration;

/**
 * Class m211217_162842_add_error_status_to_bill_table
 */
class m211217_162842_add_error_status_to_bill_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = "ALTER TABLE bill MODIFY status ENUM('draft','completed','closed','error') default 'draft'";
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211217_162842_add_error_status_to_bill_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211217_162842_add_error_status_to_bill_table cannot be reverted.\n";

        return false;
    }
    */
}
