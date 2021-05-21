<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%report_changes_companies}}`.
 */
class m210521_154711_create_report_changes_companies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%report_changes_companies}}', [
            'id_report_change_company' => $this->primaryKey(),
            'customer_id_customer' => $this->integer(),
            'new_business_name' => $this->string(),
            'old_business_name' => $this->string(),
            'date' => $this->date()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%report_changes_companies}}');
    }
}
