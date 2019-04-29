<?php

use yii\db\Migration;

class m160801_193755_contract_detail_applied extends Migration
{
    public function up()
    {

        $this->execute("ALTER TABLE `contract_detail`ADD COLUMN `applied` INT(11) NULL DEFAULT 0 AFTER `discount_id`;");

    }

    public function down()
    {
        echo "m160801_193755_contract_detail_applied cannot be reverted.\n";

        return false;
    }

}