<?php

use yii\db\Migration;

class m160818_185357_vendor_ticket_user extends Migration
{

    public function up() {

        $this->execute("ALTER TABLE vendor ADD COLUMN external_user_id INT(11) NOT NULL");

        return true;
    }

    public function down() {

        return true;
    }

}
