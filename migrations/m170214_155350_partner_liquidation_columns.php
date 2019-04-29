<?php

use yii\db\Migration;

class m170214_155350_partner_liquidation_columns extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE partner_liquidation DROP COLUMN last_account_movement_id" );
        $this->execute("create table partner_liquidation_movement (
          partner_liquidation_movement_id INT AUTO_INCREMENT not null,
          partner_liquidation_id INT not null,
          class varchar(255) not null,
          model_id INT not null,
          type as varchar(255) not null,
          PRIMARY KEY (partner_liquidation_movement_id),
          FOREIGN KEY (partner_liquidation_id) REFERENCES partner_liquidation (partner_liquidation_id)
        );");
        $this->execute("CREATE INDEX ix_partner_liquidation_movement_index ON partner_liquidation_movement (class, model_id, type);");
    }

    public function down()
    {
        echo "m170214_155350_partner_liquidation_columns cannot be reverted.\n";

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
