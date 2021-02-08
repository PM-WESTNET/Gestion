<?php

use yii\db\Migration;

class m160810_142405_partner_modifications extends Migration
{
    public function up()
    {
        $this->execute("delete from partner_liquidation;");
        $this->execute("ALTER TABLE partner_liquidation DROP FOREIGN KEY fk_partner_liquidation_partner_distribution_model1;");
        $this->execute("ALTER TABLE partner_liquidation drop column partner_distribution_model_id;");
        $this->execute("ALTER TABLE partner_liquidation ADD COLUMN partner_distribution_model_has_partner_id INT(11) NOT NULL AFTER last_account_movement_id;");
        $this->execute("ALTER TABLE partner_liquidation ADD COLUMN debit DOUBLE NULL DEFAULT 0 AFTER partner_distribution_model_has_partner_id;");
        $this->execute("ALTER TABLE partner_liquidation ADD COLUMN credit DOUBLE NULL DEFAULT 0 AFTER debit;");

        $this->execute("ALTER TABLE partner_liquidation ADD INDEX fk_partner_liquidation_partner_distribution_model_has_partn_idx (partner_distribution_model_has_partner_id ASC);");
        $this->execute("ALTER TABLE partner_liquidation ADD CONSTRAINT fk_partner_liquidation_partner_distribution_model_has_partner1
              FOREIGN KEY (partner_distribution_model_has_partner_id)
              REFERENCES westnet.partner_distribution_model_has_partner (partner_distribution_model_has_partner_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;");
    }



    public function down()
    {
        echo "m160810_142405_partner_modifications cannot be reverted.\n";

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
