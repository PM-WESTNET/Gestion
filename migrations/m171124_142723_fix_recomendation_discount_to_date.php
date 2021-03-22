<?php

use yii\db\Migration;

class m171124_142723_fix_recomendation_discount_to_date extends Migration
{

    public function up()
    {
        $this->execute("UPDATE customer_has_discount SET to_date = DATE_SUB(to_date, INTERVAL 1 DAY) WHERE discount_id = 42 AND to_date >= '2017-12-01'");
    }

    public function down()
    {
        echo "m171124_142723_fix_recomendation_discount_to_date cannot be reverted.\n";

        return false;
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
