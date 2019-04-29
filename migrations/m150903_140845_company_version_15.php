<?php

use yii\db\Schema;
use yii\db\Migration;

class m150903_140845_company_version_15 extends Migration
{

    public function up()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->execute('ALTER TABLE bill DROP FOREIGN KEY fk_bill_customer1;');
        $this->execute('ALTER TABLE customer DROP FOREIGN KEY fk_customer_customer_type1;');
        $this->execute('ALTER TABLE profile DROP FOREIGN KEY fk_profile_customer1;');
        $this->execute('ALTER TABLE payment DROP FOREIGN KEY fk_payment_customer1;');
        $this->execute('ALTER TABLE payment_receipt DROP FOREIGN KEY fk_payment_receipt_customer1;');
        $this->execute('ALTER TABLE account_movement DROP FOREIGN KEY fk_account_movement_money_box_account1;');
        $this->execute('ALTER TABLE account_movement DROP FOREIGN KEY fk_account_movement_account1;');

        $this->execute('CREATE TABLE IF NOT EXISTS point_of_sale (
              point_of_sale_id INT(11) NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NOT NULL ,
              number INT(11) NOT NULL ,
              status ENUM(\'enabled\',\'disabled\') NULL DEFAULT NULL ,
              description VARCHAR(255) NULL DEFAULT NULL ,
              company_id INT(11) NOT NULL ,
              `default` TINYINT(1) NULL DEFAULT NULL ,
              PRIMARY KEY (point_of_sale_id)  ,
              INDEX fk_point_of_sale_company1_idx (company_id ASC)  ,
              CONSTRAINT fk_sale_point_company1
                FOREIGN KEY (company_id)
                REFERENCES company (company_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS tax_condition (
              tax_condition_id INT(11) NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NULL DEFAULT NULL ,
              exempt TINYINT(1) NULL DEFAULT NULL ,
              document_type_id INT(11) NULL ,
              PRIMARY KEY (tax_condition_id)  ,
              INDEX fk_tax_condition_document_type1_idx (document_type_id ASC)  )
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('INSERT INTO tax_condition SELECT * FROM customer_type;');
        $this->execute('DROP TABLE customer_type;');

        $this->execute('ALTER TABLE stock_movement ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER bill_detail_id;');
        $this->execute('ALTER TABLE stock_movement ADD INDEX ix_stock_movement_company_id (company_id ASC)  ');

        $this->execute('ALTER TABLE bill ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER observation;');
        $this->execute('ALTER TABLE bill ADD INDEX ix_bill_company_id (company_id ASC)  ;');

        $this->execute('ALTER TABLE customer ADD COLUMN account_id INT(11) NOT NULL  AFTER document_type_id;');
        $this->execute('ALTER TABLE customer ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER account_id;');
        $this->execute('ALTER TABLE customer CHANGE COLUMN customer_type_id tax_condition_id INT(11) NOT NULL  AFTER company_id;');
        $this->execute('ALTER TABLE customer ADD INDEX fk_customer_account1_idx (account_id ASC)  ;');
        $this->execute('ALTER TABLE customer ADD INDEX ix_customer_company_id (company_id ASC)  ;');
        $this->execute('ALTER TABLE customer ADD INDEX fk_customer_tax_condition1_idx (tax_condition_id ASC)  ;');
        //$this->execute('ALTER TABLE customer DROP INDEX fk_customer_customer_type1_idx ;');
        $this->execute('ALTER TABLE payment ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER balance;');
        $this->execute('ALTER TABLE payment ADD INDEX ix_payment_company_id (company_id ASC)  ;');
        $this->execute('ALTER TABLE payment_receipt ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER payment_method_id;');
        $this->execute('ALTER TABLE provider_bill ADD COLUMN bill_type_id INT(11) NOT NULL  AFTER balance;');
        $this->execute('ALTER TABLE provider_bill ADD COLUMN status VARCHAR(45) NULL DEFAULT NULL  AFTER bill_type_id;');
        $this->execute('ALTER TABLE provider_bill ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER status;');
        $this->execute('ALTER TABLE provider_bill ADD INDEX fk_provider_bill_bill_type1_idx (bill_type_id ASC)  ;');
        $this->execute('ALTER TABLE provider_bill ADD INDEX ix_provider_bill_company_id (company_id ASC)  ;');
        $this->execute('ALTER TABLE provider ADD COLUMN account_id INT(11) NULL DEFAULT NULL  AFTER description;');
        $this->execute('ALTER TABLE provider ADD COLUMN tax_condition_id INT(11) NOT NULL  AFTER account_id;');
        $this->execute('ALTER TABLE provider ADD INDEX fk_provider_account1_idx (account_id ASC)  ;');
        $this->execute('ALTER TABLE provider ADD INDEX fk_provider_tax_condition1_idx (tax_condition_id ASC)  ;');
        $this->execute('ALTER TABLE provider_payment ADD COLUMN number VARCHAR(45) NULL DEFAULT NULL  AFTER balance;');
        $this->execute('ALTER TABLE provider_payment CHANGE COLUMN provider_id provider_id INT(11) NOT NULL  AFTER number;');
        $this->execute('ALTER TABLE provider_payment ADD COLUMN payment_method_id INT(11) NOT NULL  AFTER provider_id;');
        $this->execute('ALTER TABLE provider_payment ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER payment_method_id;');
        $this->execute('ALTER TABLE provider_payment ADD INDEX fk_provider_payment_payment_method1_idx (payment_method_id ASC)  ;');
        $this->execute('ALTER TABLE provider_payment ADD INDEX ix_provider_payment_company_id (company_id ASC)  ;');
        $this->execute('ALTER TABLE company ADD COLUMN `key` VARCHAR(255) NULL DEFAULT NULL ;');
        $this->execute('ALTER TABLE company ADD COLUMN create_timestamp INT(11) NULL DEFAULT NULL;');
        $this->execute('ALTER TABLE company ADD COLUMN tax_condition_id INT(11) NOT NULL  AFTER create_timestamp;');
        $this->execute('ALTER TABLE company ADD COLUMN start DATE NULL DEFAULT NULL  AFTER tax_condition_id;');
        $this->execute('ALTER TABLE company ADD COLUMN iibb VARCHAR(45) NULL DEFAULT NULL  AFTER start;');
        $this->execute('ALTER TABLE company ADD COLUMN `default` TINYINT(1) NULL DEFAULT NULL  AFTER iibb;');
        $this->execute('ALTER TABLE company ADD INDEX fk_company_tax_condition1_idx (tax_condition_id ASC)  ;');



        $this->execute('CREATE TABLE IF NOT EXISTS plan_feature (
              plan_feature_id INT(11) NOT NULL AUTO_INCREMENT ,
              name VARCHAR(255) NOT NULL ,
              type ENUM(\'radiobutton\',\'checkbox\') NULL DEFAULT NULL ,
              description TEXT NULL DEFAULT NULL ,
              parent_id INT(11) NULL DEFAULT NULL ,
              PRIMARY KEY (plan_feature_id)  ,
              INDEX fk_plan_feature_1_idx (parent_id ASC)  ,
              CONSTRAINT fk_plan_feature_1
                FOREIGN KEY (parent_id)
                REFERENCES plan_feature (plan_feature_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS product_has_plan_feature (
              product_id INT(11) NOT NULL ,
              plan_feature_id INT(11) NOT NULL ,
              PRIMARY KEY (product_id, plan_feature_id)  ,
              INDEX fk_product_has_plan_feature_2_idx (plan_feature_id ASC)  ,
              CONSTRAINT fk_product_has_plan_feature_1
                FOREIGN KEY (product_id)
                REFERENCES product (product_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_product_has_plan_feature_2
                FOREIGN KEY (plan_feature_id)
                REFERENCES plan_feature (plan_feature_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');


        $this->execute('CREATE TABLE IF NOT EXISTS account_movement_item (
              account_movement_item_id INT(11) NOT NULL AUTO_INCREMENT ,
              account_id INT(11) NOT NULL ,
              account_movement_id INT(11) NOT NULL ,
              money_box_account_id INT(11) NULL DEFAULT NULL ,
              debit DOUBLE NULL DEFAULT NULL ,
              credit DOUBLE NULL DEFAULT NULL ,
              PRIMARY KEY (account_movement_item_id)  ,
              INDEX fk_account_movement_item_account1_idx (account_id ASC)  ,
              INDEX fk_account_movement_item_account_movement1_idx (account_movement_id ASC)  ,
              INDEX fk_account_movement_item_money_box_account1_idx (money_box_account_id ASC)  ,
              CONSTRAINT fk_account_movement_item_account1
                FOREIGN KEY (account_id)
                REFERENCES account (account_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_account_movement_item_account_movement1
                FOREIGN KEY (account_movement_id)
                REFERENCES account_movement (account_movement_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_account_movement_item_money_box_account1
                FOREIGN KEY (money_box_account_id)
                REFERENCES money_box_account (money_box_account_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS provider_bill_has_provider_payment (
              provider_bill_id INT(11) NOT NULL ,
              provider_payment_id INT(11) NOT NULL ,
              amount DOUBLE NULL DEFAULT NULL ,
              PRIMARY KEY (provider_bill_id, provider_payment_id)  ,
              INDEX fk_provider_bill_has_provider_payment_provider_payment1_idx (provider_payment_id ASC)  ,
              INDEX fk_provider_bill_has_provider_payment_provider_bill1_idx (provider_bill_id ASC)  ,
              CONSTRAINT fk_provider_bill_has_provider_payment_provider_bill1
                FOREIGN KEY (provider_bill_id)
                REFERENCES provider_bill (provider_bill_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_provider_bill_has_provider_payment_provider_payment1
                FOREIGN KEY (provider_payment_id)
                REFERENCES provider_payment (provider_payment_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS provider_bill_has_tax_rate (
              provider_bill_id INT(11) NOT NULL ,
              tax_rate_id INT(11) NOT NULL ,
              amount DOUBLE NULL DEFAULT NULL ,
              PRIMARY KEY (provider_bill_id, tax_rate_id)  ,
              INDEX fk_provider_bill_has_tax_rate_tax_rate1_idx (tax_rate_id ASC)  ,
              INDEX fk_provider_bill_has_tax_rate_provider_bill1_idx (provider_bill_id ASC)  ,
              CONSTRAINT fk_provider_bill_has_tax_rate_provider_bill1
                FOREIGN KEY (provider_bill_id)
                REFERENCES provider_bill (provider_bill_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_provider_bill_has_tax_rate_tax_rate1
                FOREIGN KEY (tax_rate_id)
                REFERENCES tax_rate (tax_rate_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS tax_condition_has_bill_type (
              tax_condition_id INT(11) NOT NULL ,
              bill_type_id INT(11) NOT NULL ,
              `order` INT(11) NULL DEFAULT NULL ,
              PRIMARY KEY (tax_condition_id, bill_type_id)  ,
              INDEX fk_tax_condition_has_bill_type_bill_type1_idx (bill_type_id ASC)  ,
              INDEX fk_tax_condition_has_bill_type_tax_condition1_idx (tax_condition_id ASC)  ,
              CONSTRAINT fk_tax_condition_has_bill_type_tax_condition1
                FOREIGN KEY (tax_condition_id)
                REFERENCES tax_condition (tax_condition_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_tax_condition_has_bill_type_bill_type1
                FOREIGN KEY (bill_type_id)
                REFERENCES bill_type (bill_type_id)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('ALTER TABLE account_movement DROP COLUMN money_box_account_id;');
        $this->execute('ALTER TABLE account_movement DROP COLUMN credit;');
        $this->execute('ALTER TABLE account_movement DROP COLUMN debit;');
        $this->execute('ALTER TABLE account_movement DROP COLUMN account_id;');
        $this->execute('ALTER TABLE account_movement ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER time;');
        $this->execute('ALTER TABLE account_movement ADD INDEX ix_account_movement_company_id (company_id ASC)  ;');
        //$this->execute('ALTER TABLE account_movement DROP INDEX fk_account_movement_money_box_account1_idx ;');
        //$this->execute('ALTER TABLE account_movement DROP INDEX fk_account_movement_account1_idx ;');
        $this->execute('ALTER TABLE money_box_account ADD COLUMN company_id INT(11) NULL DEFAULT NULL  AFTER money_box_id;');
        $this->execute('ALTER TABLE money_box_account ADD INDEX ix_money_box_account_company_id (company_id ASC)  ;');

        $this->execute('ALTER TABLE customer
        ADD CONSTRAINT fk_customer_account1
          FOREIGN KEY (account_id)
          REFERENCES account (account_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION,
        ADD CONSTRAINT fk_customer_tax_condition1
          FOREIGN KEY (tax_condition_id)
          REFERENCES tax_condition (tax_condition_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE provider_bill
        ADD CONSTRAINT fk_provider_bill_bill_type1
          FOREIGN KEY (bill_type_id)
          REFERENCES bill_type (bill_type_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE provider
        ADD CONSTRAINT fk_provider_account1
          FOREIGN KEY (account_id)
          REFERENCES account (account_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION,
        ADD CONSTRAINT fk_provider_tax_condition1
          FOREIGN KEY (tax_condition_id)
          REFERENCES tax_condition (tax_condition_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE provider_payment
        ADD CONSTRAINT fk_provider_payment_payment_method1
          FOREIGN KEY (payment_method_id)
          REFERENCES payment_method (payment_method_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE company
        ADD CONSTRAINT fk_company_tax_condition1
          FOREIGN KEY (tax_condition_id)
          REFERENCES tax_condition (tax_condition_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE tax_condition
        ADD CONSTRAINT fk_tax_condition_document_type1
          FOREIGN KEY (document_type_id)
          REFERENCES document_type (document_type_id)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;');
        $this->execute('SET FOREIGN_KEY_CHECKS=1');

    }

    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');

        $this->execute('ALTER TABLE company DROP FOREIGN KEY fk_company_tax_condition1;');
        $this->execute('ALTER TABLE customer DROP FOREIGN KEY fk_customer_tax_condition1;');
        $this->execute('ALTER TABLE customer DROP FOREIGN KEY fk_customer_account1;');
        $this->execute('ALTER TABLE provider DROP FOREIGN KEY fk_provider_tax_condition1;');
        $this->execute('ALTER TABLE provider DROP FOREIGN KEY fk_provider_account1;');
        $this->execute('ALTER TABLE provider_bill DROP FOREIGN KEY fk_provider_bill_bill_type1;');
        $this->execute('ALTER TABLE provider_payment DROP FOREIGN KEY fk_provider_payment_payment_method1;');

        $this->execute('CREATE TABLE IF NOT EXISTS customer_type (
              customer_type_id INT(11) NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NULL DEFAULT NULL ,
              exempt TINYINT(1) NULL DEFAULT NULL ,
              document_type_id INT(11) NULL ,
              PRIMARY KEY (customer_type_id)  ,
              INDEX fk_customer_type_document_type1_idx (document_type_id ASC)  )
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci;');

        $this->execute('INSERT INTO customer_type SELECT * FROM tax_condition;');
        $this->execute('DROP TABLE tax_condition;');

        $this->execute('ALTER TABLE account_movement DROP INDEX ix_account_movement_company_id ;');
        $this->execute('ALTER TABLE account_movement DROP COLUMN company_id;');
        $this->execute('ALTER TABLE account_movement ADD COLUMN account_id INT(11) NOT NULL AFTER account_movement_id;');
        $this->execute('ALTER TABLE account_movement ADD COLUMN debit DOUBLE NULL DEFAULT NULL AFTER description;');
        $this->execute('ALTER TABLE account_movement ADD COLUMN credit DOUBLE NULL DEFAULT NULL AFTER debit;');
        $this->execute('ALTER TABLE account_movement ADD COLUMN money_box_account_id INT(11) NULL DEFAULT NULL AFTER status;');
        $this->execute('ALTER TABLE account_movement ADD INDEX fk_account_movement_account1_idx (account_id ASC);');
        $this->execute('ALTER TABLE account_movement ADD INDEX fk_account_movement_money_box_account1_idx (money_box_account_id ASC);');


        $this->execute('ALTER TABLE bill DROP INDEX ix_bill_company_id ;');
        $this->execute('ALTER TABLE company DROP INDEX fk_company_tax_condition1_idx ;');
        $this->execute('ALTER TABLE customer DROP INDEX fk_customer_tax_condition1_idx ;');
        $this->execute('ALTER TABLE customer DROP INDEX ix_customer_company_id;');
        $this->execute('ALTER TABLE customer DROP INDEX fk_customer_account1_idx ;');
        $this->execute('ALTER TABLE money_box_account DROP INDEX ix_money_box_account_company_id ;');
        $this->execute('ALTER TABLE payment DROP INDEX ix_payment_company_id ;');
        $this->execute('ALTER TABLE provider_payment DROP INDEX ix_provider_payment_company_id ;');
        $this->execute('ALTER TABLE provider_payment DROP INDEX fk_provider_payment_payment_method1_idx ;');
        $this->execute('ALTER TABLE stock_movement DROP INDEX ix_stock_movement_company_id ;');
        $this->execute('ALTER TABLE provider DROP INDEX fk_provider_tax_condition1_idx ;');
        $this->execute('ALTER TABLE provider DROP INDEX fk_provider_account1_idx ;');
        $this->execute('ALTER TABLE provider_bill DROP INDEX ix_provider_bill_company_id ;');
        $this->execute('ALTER TABLE provider_bill DROP INDEX fk_provider_bill_bill_type1_idx ;');


        $this->execute('ALTER TABLE bill DROP COLUMN company_id;');
        $this->execute('ALTER TABLE company DROP COLUMN `default`;');
        $this->execute('ALTER TABLE company DROP COLUMN iibb;');
        $this->execute('ALTER TABLE company DROP COLUMN start;');
        $this->execute('ALTER TABLE company DROP COLUMN tax_condition_id;');
        $this->execute('ALTER TABLE company DROP COLUMN create_timestamp;');
        $this->execute('ALTER TABLE company DROP COLUMN `key`;');
        $this->execute('ALTER TABLE customer DROP COLUMN tax_condition_id;');
        $this->execute('ALTER TABLE customer DROP COLUMN company_id;');
        $this->execute('ALTER TABLE customer DROP COLUMN account_id;');
        $this->execute('ALTER TABLE customer ADD COLUMN customer_type_id INT(11) NOT NULL AFTER document_type_id;');
        $this->execute('ALTER TABLE customer ADD INDEX fk_customer_customer_type1_idx (customer_type_id ASC);');
        $this->execute('ALTER TABLE money_box_account DROP COLUMN company_id;');
        $this->execute('ALTER TABLE payment DROP COLUMN company_id;');
        $this->execute('ALTER TABLE payment_receipt DROP COLUMN company_id;');
        $this->execute('ALTER TABLE provider DROP COLUMN tax_condition_id;');
        $this->execute('ALTER TABLE provider DROP COLUMN account_id;');
        $this->execute('ALTER TABLE provider_bill DROP COLUMN company_id;');
        $this->execute('ALTER TABLE provider_bill DROP COLUMN status;');
        $this->execute('ALTER TABLE provider_bill DROP COLUMN bill_type_id;');
        $this->execute('ALTER TABLE provider_payment DROP COLUMN company_id;');
        $this->execute('ALTER TABLE provider_payment DROP COLUMN payment_method_id;');
        $this->execute('ALTER TABLE provider_payment DROP COLUMN number;');
        $this->execute('ALTER TABLE provider_payment CHANGE COLUMN provider_id provider_id INT(11) NOT NULL AFTER description;');
        $this->execute('ALTER TABLE stock_movement DROP COLUMN company_id;');
        $this->execute('DROP TABLE IF EXISTS tax_condition_has_bill_type ;');
        $this->execute('DROP TABLE IF EXISTS tax_condition ;');
        $this->execute('DROP TABLE IF EXISTS provider_bill_has_tax_rate ;');
        $this->execute('DROP TABLE IF EXISTS provider_bill_has_provider_payment ;');
        $this->execute('DROP TABLE IF EXISTS product_has_plan_feature ;');
        $this->execute('DROP TABLE IF EXISTS point_of_sale ;');
        $this->execute('DROP TABLE IF EXISTS plan_feature ;');
        $this->execute('DROP TABLE IF EXISTS account_movement_item ;');

        $this->execute('ALTER TABLE account_movement
            ADD CONSTRAINT fk_account_movement_account1
              FOREIGN KEY (account_id)
              REFERENCES account (account_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            ADD CONSTRAINT fk_account_movement_money_box_account1
              FOREIGN KEY (money_box_account_id)
              REFERENCES money_box_account (money_box_account_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE bill
            ADD CONSTRAINT fk_bill_customer1
              FOREIGN KEY (customer_id)
              REFERENCES customer (customer_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE customer
            ADD CONSTRAINT fk_customer_customer_type1
              FOREIGN KEY (customer_type_id)
              REFERENCES customer_type (customer_type_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE payment
            ADD CONSTRAINT fk_payment_customer1
              FOREIGN KEY (customer_id)
              REFERENCES customer (customer_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE payment_receipt
            ADD CONSTRAINT fk_payment_receipt_customer1
              FOREIGN KEY (customer_id)
              REFERENCES customer (customer_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;');

        $this->execute('ALTER TABLE profile
            ADD CONSTRAINT fk_profile_customer1
              FOREIGN KEY (customer_id)
              REFERENCES customer (customer_id)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;');


        $this->execute('SET FOREIGN_KEY_CHECKS=1;');

        return true;
    }

}