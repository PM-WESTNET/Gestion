<?php

use yii\db\Migration;

class m170905_190123_debt_evolution_table extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE debt_evolution(
          debt_evolution_id INT PRIMARY KEY AUTO_INCREMENT,
          period DATE,
          invoice_1 INT DEFAULT 0,
          invoice_2 INT DEFAULT 0,
          invoice_3 INT DEFAULT 0,
          invoice_4 INT DEFAULT 0,
          invoice_5 INT DEFAULT 0,
          invoice_6 INT DEFAULT 0,
          invoice_7 INT DEFAULT 0,
          invoice_8 INT DEFAULT 0,
          invoice_9 INT DEFAULT 0,
          invoice_10 INT DEFAULT 0,
          invoice_x INT DEFAULT 0
      );');
    }

    public function down()
    {
        echo "m170905_190123_debt_evolution_table cannot be reverted.\n";

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
