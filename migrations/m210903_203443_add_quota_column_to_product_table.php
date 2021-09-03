<?php

use yii\db\Migration;

/**
 * Handles adding quota to table `product`.
 */
class m210903_203443_add_quota_column_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}','quota', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}','quota');
    }
}
