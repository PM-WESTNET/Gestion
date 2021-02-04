<?php

use yii\db\Migration;

class m190506_172727_create_report_company_data_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('report_company_data', [
            'report_company_data_id' => $this->primaryKey(),
            'report' => $this->text(50),
            'period'=> $this->integer(6),
            'value' => $this->double(),
            'company_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_report_company_data_company_id', 'report_company_data', 'company_id', 'company', 'company_id');
    }

    public function safeDown()
    {
        $this->dropTable('report_company_data');
    }
}
