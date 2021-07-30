<?php

use yii\db\Migration;

/**
 * Handles adding description to table `customer`.
 */
class m210730_182004_add_description_column_to_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer}}','description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}','description');
    }
}
