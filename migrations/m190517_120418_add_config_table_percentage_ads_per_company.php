<?php

use yii\db\Migration;

class m190517_120418_add_config_table_percentage_ads_per_company extends Migration
{
    public function safeUp()
    {
        $this->createTable('ads_percentage_per_company', [
            'percentage_per_company_id' => $this->primaryKey(),
            'parent_company_id' => $this->integer(),
            'company_id' => $this->integer(),
            'percentage' => $this->float()
        ]);

        $this->addForeignKey('fk_ads_percentage_per_company_parent_company_id', 'ads_percentage_per_company', 'parent_company_id', 'company', 'company_id');
        $this->addForeignKey('fk_ads_percentage_per_company_company_id', 'ads_percentage_per_company', 'company_id', 'company', 'company_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ads_percentage_per_company_company_id', 'ads_percentage_per_company');
        $this->dropForeignKey('fk_ads_percentage_per_company_parent_company_id', 'ads_percentage_per_company');

        $this->dropTable('ads_percentage_per_company');
    }
}
