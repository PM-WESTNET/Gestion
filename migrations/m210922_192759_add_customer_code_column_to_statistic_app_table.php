<?php

use yii\db\Migration;

/**
 * Handles adding customer_code to table `statistic_app`.
 */
class m210922_192759_add_customer_code_column_to_statistic_app_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%statistic_app}}', 'customer_code', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%statistic_app}}','customer_code');
    }
}
