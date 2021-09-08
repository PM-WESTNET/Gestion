<?php

use yii\db\Migration;

/**
 * Handles adding big_plan to table `product`.
 */
class m210908_125542_add_big_plan_column_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}','big_plan', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}','big_plan');
    }
}
