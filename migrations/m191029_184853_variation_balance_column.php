<?php

use yii\db\Migration;

/**
 * Class m191029_184853_variation_balance_column
 */
class m191029_184853_variation_balance_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('conciliation_item', 'variation_balance', 'DOUBLE NOT NULL DEFAULT 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('conciliation_item', 'variation_balance');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191029_184853_variation_balance_column cannot be reverted.\n";

        return false;
    }
    */
}
