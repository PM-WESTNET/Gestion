<?php

use yii\db\Migration;

/**
 * Class m190801_200856_ready_column_resume_item_and_conciliation_item
 */
class m190801_200856_ready_column_resume_item_and_conciliation_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('resume_item', 'ready', 'BOOLEAN NOT NULL DEFAULT 0');
        $this->addColumn('account_movement_item', 'ready', 'BOOLEAN NOT NULL DEFAULT 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('resume_item', 'ready');
        $this->dropColumn('account_movement_item', 'ready');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190801_200856_ready_column_resume_item_and_conciliation_item cannot be reverted.\n";

        return false;
    }
    */
}
