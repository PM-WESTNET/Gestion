<?php

use yii\db\Migration;

class m180815_141257_type_column_mobile_push extends Migration
{
    public function up()
    {
        $this->addColumn('mobile_push', 'type', 'ENUM("default", "invoice") NULL DEFAULT "default"');
    }

    public function down()
    {
        $this->dropColumn('mobile_push', 'type');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
