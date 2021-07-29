<?php

use yii\db\Migration;

/**
 * Handles adding company_id to table `siro_payment_intention`.
 */
class m210716_173424_add_company_id_columns_to_siro_payment_intention_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   
        $this->addColumn('{{%siro_payment_intention}}', 'company_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%siro_payment_intention}}','company_id');
    }
}
