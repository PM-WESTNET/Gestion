<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_000001_base_tables extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE account (
            account_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            is_usable TINYINT(1),
            code VARCHAR(45),
            lft INT(11),
            rgt INT(11),
            parent_account_id INT(11),
            CONSTRAINT fk_account_account1 FOREIGN KEY (parent_account_id) REFERENCES account (account_id)
          );");
        $this->execute("CREATE INDEX fk_account_account1_idx ON account (parent_account_id);");

        $this->execute("CREATE TABLE accounting_period (
            accounting_period_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150),
            date_from DATE,
            date_to DATE,
            number INT(11),
            status ENUM('open', 'closed')
        );");

        $this->execute("CREATE TABLE account_config (
            account_config_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            class VARCHAR(250) NOT NULL,
            classMovement VARCHAR(250) NOT NULL
        );");

        $this->execute("CREATE TABLE account_config_has_account (
            account_config_has_account_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            account_config_id INT(11) NOT NULL,
            account_id INT(11) NOT NULL,
            is_debit TINYINT(1),
            attrib VARCHAR(45),
            CONSTRAINT fk_account_config_has_account_account_config1 FOREIGN KEY (account_config_id) REFERENCES account_config (account_config_id),
            CONSTRAINT fk_account_config_has_account_account1 FOREIGN KEY (account_id) REFERENCES account (account_id)
        );");
        $this->execute("CREATE INDEX fk_account_config_has_account_account1_idx ON account_config_has_account (account_id);");
        $this->execute("CREATE INDEX fk_account_config_has_account_account_config1_idx ON account_config_has_account (account_config_id);");

        $this->execute("CREATE TABLE account_movement (
            account_movement_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            description VARCHAR(150),
            status ENUM('draft', 'closed', 'broken') DEFAULT 'draft' NOT NULL,
            date DATE,
            time TIME,
            company_id INT(11),
            accounting_period_id INT(11) NOT NULL,
            `check` TINYINT(1) DEFAULT '0',
            CONSTRAINT fk_account_movement_accounting_period1 FOREIGN KEY (accounting_period_id) REFERENCES accounting_period (accounting_period_id)
        );");
        $this->execute("CREATE INDEX fk_account_movement_accounting_period1_idx ON account_movement (accounting_period_id);");
        $this->execute("CREATE INDEX ix_account_movement_company_id ON account_movement (company_id);");

        $this->execute("CREATE TABLE account_movement_item (
            account_movement_item_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            account_id INT(11) NOT NULL,
            account_movement_id INT(11) NOT NULL,
            debit DOUBLE,
            credit DOUBLE,
            status ENUM('draft', 'closed', 'conciled') DEFAULT 'draft' NOT NULL,
            `check` TINYINT(1) DEFAULT '0',
            CONSTRAINT fk_account_movement_item_account1 FOREIGN KEY (account_id) REFERENCES account (account_id),
            CONSTRAINT fk_account_movement_item_account_movement1 FOREIGN KEY (account_movement_id) REFERENCES account_movement (account_movement_id)
        );");
        $this->execute("CREATE INDEX fk_account_movement_item_account1_idx ON account_movement_item (account_id);");
        $this->execute("CREATE INDEX fk_account_movement_item_account_movement1_idx ON account_movement_item (account_movement_id);");

        $this->execute("CREATE TABLE account_movement_relation (
            account_movement_relation_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            class VARCHAR(100) NOT NULL,
            model_id INT(11) NOT NULL,
            account_movement_id INT(11),
            CONSTRAINT fk_account_movement_relation_account_movement1 FOREIGN KEY (account_movement_id) REFERENCES account_movement (account_movement_id)
        );");
        $this->execute("CREATE INDEX fk_account_movement_relation_account_movement1_idx ON account_movement_relation (account_movement_id);");
        $this->execute("CREATE INDEX ix_account_movement_relation_id ON account_movement_relation (class, model_id);");


        $this->execute("CREATE TABLE operation_type (
            operation_type_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            is_debit TINYINT(1),
            code VARCHAR(45) NOT NULL
        );");

        $this->execute("CREATE TABLE money_box_type (
            money_box_type_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            code VARCHAR(45)
        );");

        $this->execute("CREATE TABLE money_box (
            money_box_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            money_box_type_id INT(11) NOT NULL,
            account_id INT(11),
            CONSTRAINT fk_money_box_money_box_type1 FOREIGN KEY (money_box_type_id) REFERENCES money_box_type (money_box_type_id),
            CONSTRAINT fk_money_box_account1 FOREIGN KEY (account_id) REFERENCES account (account_id)
        );");
        $this->execute("CREATE INDEX fk_money_box_account1_idx ON money_box (account_id);");
        $this->execute("CREATE INDEX fk_money_box_money_box_type1_idx ON money_box (money_box_type_id);");

        $this->execute("CREATE TABLE money_box_account (
            money_box_account_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            number VARCHAR(45) NOT NULL,
            enable TINYINT(1),
            money_box_id INT(11) NOT NULL,
            company_id INT(11),
            account_id INT(11),
            currency_id INT(11) NOT NULL,
            small_box TINYINT(1),
            daily_box_last_closing_date DATE,
            daily_box_last_closing_time TIME,
            type ENUM('common', 'daily', 'small') DEFAULT 'common' NOT NULL,
            CONSTRAINT fk_bank_account_bank1 FOREIGN KEY (money_box_id) REFERENCES money_box (money_box_id),
            CONSTRAINT fk_money_box_account_account1 FOREIGN KEY (account_id) REFERENCES account (account_id),
            CONSTRAINT fk_money_box_account_currency1 FOREIGN KEY (currency_id) REFERENCES currency (currency_id)
        );");
        $this->execute("CREATE INDEX fk_bank_account_bank1_idx ON money_box_account (money_box_id);");
        $this->execute("CREATE INDEX fk_money_box_account_account1_idx ON money_box_account (account_id);");
        $this->execute("CREATE INDEX fk_money_box_account_currency1_idx ON money_box_account (currency_id);");
        $this->execute("CREATE INDEX ix_money_box_account_company_id ON money_box_account (company_id);");
        $this->execute("CREATE TABLE money_box_has_operation_type (
            money_box_has_operation_type_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            operation_type_id INT(11) NOT NULL,
            money_box_id INT(11) NOT NULL,
            account_id INT(11) NOT NULL,
            money_box_account_id INT(11),
            CONSTRAINT fk_money_box_has_operation_type_operation_type1 FOREIGN KEY (operation_type_id) REFERENCES operation_type (operation_type_id),
            CONSTRAINT fk_money_box_has_operation_type_money_box1 FOREIGN KEY (money_box_id) REFERENCES money_box (money_box_id),
            CONSTRAINT fk_money_box_has_operation_type_account1 FOREIGN KEY (account_id) REFERENCES account (account_id),
            CONSTRAINT fk_money_box_has_operation_type_money_box_account1 FOREIGN KEY (money_box_account_id) REFERENCES money_box_account (money_box_account_id)
        );");
        $this->execute("CREATE INDEX fk_money_box_has_operation_type_account1_idx ON money_box_has_operation_type (account_id);");
        $this->execute("CREATE INDEX fk_money_box_has_operation_type_money_box1_idx ON money_box_has_operation_type (money_box_id);");
        $this->execute("CREATE INDEX fk_money_box_has_operation_type_money_box_account1_idx ON money_box_has_operation_type (money_box_account_id);");
        $this->execute("CREATE INDEX fk_money_box_has_operation_type_operation_type1_idx ON money_box_has_operation_type (operation_type_id);");
        $this->execute("CREATE UNIQUE INDEX ix_money_box_id_operation_type_id_UNIQUE ON money_box_has_operation_type (operation_type_id, money_box_id, account_id);");

        $this->execute("CREATE TABLE resume (
            resume_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            money_box_account_id INT(11) NOT NULL,
            name VARCHAR(150) NOT NULL,
            date DATE,
            date_from DATE,
            date_to DATE,
            status ENUM('draft', 'closed', 'canceled', 'conciled'),
            balance_initial DOUBLE,
            balance_final DOUBLE,
            company_id INT(11),
            CONSTRAINT fk_resume_money_box_account1 FOREIGN KEY (money_box_account_id) REFERENCES money_box_account (money_box_account_id)
        );");
        $this->execute("CREATE INDEX fk_resume_money_box_account1_idx ON resume (money_box_account_id);");
        $this->execute("CREATE INDEX ix_resume_company_id ON resume (company_id);");

        $this->execute("CREATE TABLE resume_item (
            resume_item_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            resume_id INT(11),
            description VARCHAR(150),
            reference VARCHAR(45),
            code VARCHAR(45),
            debit DOUBLE,
            credit DOUBLE,
            status ENUM('draft', 'closed', 'conciled'),
            date DATE,
            money_box_has_operation_type_id INT(11) NOT NULL,
            CONSTRAINT fk_resume_item_resume1 FOREIGN KEY (resume_id) REFERENCES resume (resume_id),
            CONSTRAINT fk_resume_item_money_box_has_operation_type1 FOREIGN KEY (money_box_has_operation_type_id) REFERENCES money_box_has_operation_type (money_box_has_operation_type_id)
        );");
        $this->execute("CREATE INDEX fk_resume_item_money_box_has_operation_type1_idx ON resume_item (money_box_has_operation_type_id);");
        $this->execute("CREATE INDEX fk_resume_item_resume1_idx ON resume_item (resume_id);");


        $this->execute("CREATE TABLE conciliation (
            conciliation_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            name VARCHAR(150) NOT NULL,
            date DATE,
            date_from DATE,
            date_to DATE,
            status ENUM('draft', 'closed'),
            timestamp TIMESTAMP,
            money_box_account_id INT(11) NOT NULL,
            company_id INT(11),
            resume_id INT(11) NOT NULL,
            CONSTRAINT fk_conciliation_money_box_account1 FOREIGN KEY (money_box_account_id) REFERENCES money_box_account (money_box_account_id),
            CONSTRAINT fk_conciliation_resume1 FOREIGN KEY (resume_id) REFERENCES resume (resume_id)
        );");
        $this->execute("CREATE INDEX fk_conciliation_money_box_account1_idx ON conciliation (money_box_account_id);");
        $this->execute("CREATE INDEX fk_conciliation_resume1_idx ON conciliation (resume_id);");
        $this->execute("CREATE INDEX ix_conciliation_company_id ON conciliation (company_id);");
        $this->execute("CREATE TABLE conciliation_item (
            conciliation_item_id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            conciliation_id INT(11) NOT NULL,
            amount DOUBLE,
            date DATE,
            description VARCHAR(150),
            CONSTRAINT fk_conciliation_item_conciliation1 FOREIGN KEY (conciliation_id) REFERENCES conciliation (conciliation_id)
        );");
        $this->execute("CREATE INDEX fk_conciliation_item_conciliation1_idx ON conciliation_item (conciliation_id);");

        $this->execute("CREATE TABLE conciliation_item_has_account_movement_item (
            account_movement_item_id INT(11) NOT NULL,
            conciliation_item_id INT(11) NOT NULL,
            CONSTRAINT `PRIMARY` PRIMARY KEY (account_movement_item_id, conciliation_item_id),
            CONSTRAINT fk_conciliation_item_has_account_movement_item_account_movem1 FOREIGN KEY (account_movement_item_id) REFERENCES account_movement_item (account_movement_item_id),
            CONSTRAINT fk_conciliation_item_has_account_movement_item_conciliation_1 FOREIGN KEY (conciliation_item_id) REFERENCES conciliation_item (conciliation_item_id)
        );");

        $this->execute("CREATE INDEX fk_conciliation_item_has_account_movement_item_conciliation_idx ON conciliation_item_has_account_movement_item (conciliation_item_id);");
        $this->execute("CREATE TABLE conciliation_item_has_resume_item (
            conciliation_item_id INT(11) NOT NULL,
            resume_item_id INT(11) NOT NULL,
            CONSTRAINT `PRIMARY` PRIMARY KEY (conciliation_item_id, resume_item_id),
            CONSTRAINT fk_conciliation_item_has_resume_item_conciliation_item1 FOREIGN KEY (conciliation_item_id) REFERENCES conciliation_item (conciliation_item_id),
            CONSTRAINT fk_conciliation_item_has_resume_item_resume_item1 FOREIGN KEY (resume_item_id) REFERENCES resume_item (resume_item_id)
        );");
        $this->execute("CREATE INDEX fk_conciliation_item_has_resume_item_resume_item1_idx ON conciliation_item_has_resume_item (resume_item_id);");

    }

    public function down()
    {
        echo "m150801_184839_base_tables cannot be reverted.\n";

        return false;
    }
}