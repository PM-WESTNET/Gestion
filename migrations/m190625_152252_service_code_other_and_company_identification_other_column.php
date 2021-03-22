<?php

use yii\db\Migration;

/**
 * Class m190625_152252_service_code_other_and_company_identification_other_column
 */
class m190625_152252_service_code_other_and_company_identification_other_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bank_company_config', 'other_service_code', 'VARCHAR(45) NULL');
        $this->addColumn('bank_company_config', 'other_company_identification', 'VARCHAR(45) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('bank_company_config', 'other_service_code');
        $this->dropColumn('bank_company_config', 'other_company_identification');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_152252_service_code_other_and_company_identification_other_column cannot be reverted.\n";

        return false;
    }
    */
}
