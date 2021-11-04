<?php

use yii\db\Migration;

/**
 * Class m211104_201930_add_description_to_payment_plan_table
 */
class m211104_201930_add_description_to_payment_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_plan}}', 'description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_plan}}','description');
    }
}
