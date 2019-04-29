<?php

use yii\db\Migration;

class m181122_124810_add_table_user_has_company extends Migration
{
    public function safeUp()
    {

        $this->createTable('user_has_company', [
           'user_has_company_id' => $this->primaryKey(),
           'user_id' => $this->integer(),
           'company_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk_user_has_access_to_company_user_id', 'user_has_company', 'user_id', 'user', 'id');
        $this->addForeignKey('fk_user_has_access_to_company_company_id', 'user_has_company', 'company_id', 'company', 'company_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_has_access_to_company_company_id');
        $this->dropForeignKey('fk_user_has_access_to_company_company_id');

        $this->dropTable('user_has_company');
    }

}
