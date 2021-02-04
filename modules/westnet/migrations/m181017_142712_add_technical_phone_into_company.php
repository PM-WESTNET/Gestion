<?php

use yii\db\Migration;

class m181017_142712_add_technical_phone_into_company extends Migration
{

    public function safeUp()
    {
        $this->addColumn('company', 'technical_service_phone', $this->text(45));
    }

    public function safeDown()
    {
        $this->dropColumn('company', 'technical_service_phone');

        return false;
    }
}
