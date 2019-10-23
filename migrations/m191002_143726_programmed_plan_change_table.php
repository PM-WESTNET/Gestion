<?php

use yii\db\Migration;

/**
 * Class m191002_143726_programmatic_change_plan_table
 */
class m191002_143726_programmed_plan_change_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('programmed_plan_change', [
            'programmed_plan_change_id' => $this->primaryKey(),
            'date' => $this->integer()->notNull(),
            'applied' => $this->boolean(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'contract_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_programmed_plan_change_user_id', 'programmed_plan_change', 'user_id', 'user', 'id');
        $this->addForeignKey('fk_programmed_plan_change_product_id', 'programmed_plan_change', 'product_id', 'product', 'product_id');
        $this->addForeignKey('fk_programmed_plan_change_contract_id', 'programmed_plan_change', 'contract_id', 'contract', 'contract_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('programmed_plan_change');
    }
}
