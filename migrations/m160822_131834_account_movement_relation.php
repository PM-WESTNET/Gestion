<?php

use yii\db\Migration;

class m160822_131834_account_movement_relation extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS account_movement_relation (
              account_movement_relation_id INT(11) NOT NULL AUTO_INCREMENT,
              class VARCHAR(100) NOT NULL,
              model_id INT(11) NOT NULL,
              account_movement_id INT(11) NULL DEFAULT NULL,
              PRIMARY KEY (account_movement_relation_id),
              INDEX ix_account_movement_relation_id (class ASC, model_id ASC),
              INDEX fk_account_movement_relation_account_movement1_idx (account_movement_id ASC),
              CONSTRAINT fk_account_movement_relation_account_movement1
                FOREIGN KEY (account_movement_id)
                REFERENCES account_movement (account_movement_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
        ");

    }

    public function down()
    {
        $this->dropTable("account_movement_relation");

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
