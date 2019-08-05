<?php

use yii\db\Migration;

/**
 * Class m190625_150652_service_code_bank_company_config
 */
class m190625_150652_service_code_bank_company_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bank_company_config', 'service_code', 'VARCHAR(45) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('bank_company_config', 'service_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_150652_service_code_bank_company_config cannot be reverted.\n";

        return false;
    }
    */
}
