<?php

use yii\db\Schema;
use yii\db\Migration;

class m151111_040517_paycheck_accounting extends Migration
{
    public function up()
    {
        $this->execute('SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;');
        $this->execute('SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;');
        $this->execute("SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'TRADITIONAL,ALLOW_INVALID_DATES';");


        $this->execute("CREATE TABLE IF NOT EXISTS checkbook (
                        checkbook_id         INT(11)    NOT NULL AUTO_INCREMENT COMMENT '',
                        start_number         INT(11)    NOT NULL COMMENT '',
                        end_number           INT(11)    NOT NULL COMMENT '',
                        enabled              TINYINT(1) NULL     DEFAULT NULL COMMENT '',
                        money_box_account_id INT(11)    NOT NULL COMMENT '',
                        last_used            INT(11)    NULL     DEFAULT NULL COMMENT '',
                        PRIMARY KEY (checkbook_id)  COMMENT '',
                        INDEX fk_checkbook_money_box_account1_idx (money_box_account_id ASC) COMMENT '',
                        CONSTRAINT fk_checkbook_money_box_account1
                        FOREIGN KEY (money_box_account_id)
                        REFERENCES money_box_account (money_box_account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                        ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("CREATE TABLE IF NOT EXISTS paycheck (
                        paycheck_id     INT(11)      NOT NULL AUTO_INCREMENT    COMMENT '',
                        date            DATE         NULL     DEFAULT NULL    COMMENT '',
                        due_date        DATE         NULL     DEFAULT NULL    COMMENT '',
                        number          VARCHAR(45)  NULL     DEFAULT NULL    COMMENT '',
                        amount          DOUBLE       NULL     DEFAULT NULL    COMMENT '',
                        document_number VARCHAR(45)  NULL     DEFAULT NULL    COMMENT '',
                        status          VARCHAR(45)  NULL     DEFAULT NULL    COMMENT '',
                        business_name   VARCHAR(255) NULL     DEFAULT NULL    COMMENT '',
                        description     VARCHAR(255) NULL     DEFAULT NULL    COMMENT '',
                        is_own          TINYINT(1)   NULL     DEFAULT NULL    COMMENT '',
                        timestamp       TIMESTAMP    NULL     DEFAULT NULL    COMMENT '',
                        checkbook_id    INT(11)      NULL     DEFAULT NULL    COMMENT '',
                        money_box_id    INT(11)      NULL     DEFAULT NULL    COMMENT '',
                        crossed         TINYINT(1)   NULL     DEFAULT NULL    COMMENT '',
                        to_order        TINYINT(1)   NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (paycheck_id)        COMMENT '',
                        INDEX fk_paycheck_checkbook1_idx (checkbook_id ASC)        COMMENT '',
                        INDEX fk_paycheck_money_box1_idx (money_box_id ASC)        COMMENT '',
                        CONSTRAINT fk_paycheck_checkbook1
                        FOREIGN KEY (checkbook_id)
                        REFERENCES checkbook (checkbook_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_paycheck_money_box1
                        FOREIGN KEY (money_box_id)
                        REFERENCES money_box (money_box_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("CREATE TABLE IF NOT EXISTS paycheck_log (
                        paycheck_log_id INT(11)      NOT NULL AUTO_INCREMENT    COMMENT '',
                        paycheck_id     INT(11)      NOT NULL    COMMENT '',
                        date            DATE         NULL     DEFAULT NULL    COMMENT '',
                        time            TIME         NULL     DEFAULT NULL    COMMENT '',
                        status          VARCHAR(45)  NULL     DEFAULT NULL    COMMENT '',
                        comment         VARCHAR(255) NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (paycheck_log_id)        COMMENT '',
                        INDEX fk_paycheck_log_paycheck1_idx (paycheck_id ASC)        COMMENT '',
                        CONSTRAINT fk_paycheck_log_paycheck1
                        FOREIGN KEY (paycheck_id)
                        REFERENCES paycheck (paycheck_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("CREATE TABLE IF NOT EXISTS operation_type (
                        operation_type_id INT(11)      NOT NULL AUTO_INCREMENT    COMMENT '',
                        name              VARCHAR(150) NULL     DEFAULT NULL    COMMENT '',
                        code              VARCHAR(45)  NULL     DEFAULT NULL    COMMENT '',
                        is_debit          TINYINT(1)   NULL     DEFAULT NULL    COMMENT '',
                        account_id        INT(11)      NOT NULL    COMMENT '',
                        PRIMARY KEY (operation_type_id)        COMMENT '',
                        INDEX fk_operation_type_account1_idx (account_id ASC)        COMMENT '',
                        CONSTRAINT fk_operation_type_account1
                        FOREIGN KEY (account_id)
                        REFERENCES account (account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("CREATE TABLE IF NOT EXISTS resume (
                        resume_id  INT(11) NOT NULL AUTO_INCREMENT    COMMENT '',
                        money_box_account_id INT(11) NOT NULL    COMMENT '',
                        name VARCHAR(150) NOT NULL    COMMENT '',
                        date  DATE NULL     DEFAULT NULL    COMMENT '',
                        date_from  DATE NULL     DEFAULT NULL    COMMENT '',
                        date_to  DATE  NULL     DEFAULT NULL    COMMENT '',
                        status   ENUM('draft', 'closed', 'canceled', 'conciled') NULL     DEFAULT NULL    COMMENT '',
                        company_id  INT(11) NULL     DEFAULT NULL COMMENT '',
                        balance_initial      DOUBLE NULL     DEFAULT NULL    COMMENT '',
                        balance_final  DOUBLE NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (resume_id)        COMMENT '',
                        INDEX fk_resume_money_box_account1_idx (money_box_account_id ASC) COMMENT '',
                        INDEX ix_resume_company_id (company_id ASC) COMMENT '',
                        CONSTRAINT fk_resume_money_box_account1
                        FOREIGN KEY (money_box_account_id)
                        REFERENCES money_box_account (money_box_account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS resume_item (
                        resume_item_id    INT(11) NOT NULL AUTO_INCREMENT    COMMENT '',
                        resume_id         INT(11)                             NOT NULL    COMMENT '',
                        operation_type_id INT(11)                             NULL     DEFAULT NULL    COMMENT '',
                        description       VARCHAR(150)                        NULL     DEFAULT NULL    COMMENT '',
                        reference         VARCHAR(45)                         NULL     DEFAULT NULL    COMMENT '',
                        code              VARCHAR(45)                         NULL     DEFAULT NULL    COMMENT '',
                        debit             DOUBLE                              NULL     DEFAULT NULL    COMMENT '',
                        credit            DOUBLE                              NULL     DEFAULT 0    COMMENT '',
                        status            ENUM('draft', 'closed', 'conciled') NULL     DEFAULT NULL    COMMENT '',
                        date              DATE                                NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (resume_item_id)        COMMENT '',
                        INDEX fk_resume_item_resume1_idx (resume_id ASC)        COMMENT '',
                        INDEX fk_resume_item_operation_type1_idx (operation_type_id ASC)        COMMENT '',
                        CONSTRAINT fk_resume_item_resume1
                        FOREIGN KEY (resume_id)
                        REFERENCES resume (resume_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_resume_item_operation_type1
                        FOREIGN KEY (operation_type_id)
                        REFERENCES operation_type (operation_type_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");


        $this->execute("CREATE TABLE IF NOT EXISTS conciliation (
                        conciliation_id      INT(11)                 NOT NULL AUTO_INCREMENT    COMMENT '',
                        name                 VARCHAR(150)            NOT NULL    COMMENT '',
                        date                 DATE                    NULL     DEFAULT NULL    COMMENT '',
                        date_from            DATE                    NULL     DEFAULT NULL    COMMENT '',
                        date_to              DATE                    NULL     DEFAULT NULL    COMMENT '',
                        status               ENUM('draft', 'closed') NULL     DEFAULT NULL    COMMENT '',
                        timestamp            INT(11)                 NULL     DEFAULT NULL    COMMENT '',
                        money_box_account_id INT(11)                 NOT NULL    COMMENT '',
                        company_id           INT(11)                 NULL     DEFAULT NULL    COMMENT '',
                        resume_id            INT(11)                 NOT NULL    COMMENT '',
                        PRIMARY KEY (conciliation_id)        COMMENT '',
                        INDEX fk_conciliation_money_box_account1_idx (money_box_account_id ASC)        COMMENT '',
                        INDEX ix_conciliation_company_id (company_id ASC)        COMMENT '',
                        INDEX fk_conciliation_resume1_idx (resume_id ASC)        COMMENT '',
                        CONSTRAINT fk_conciliation_money_box_account1
                        FOREIGN KEY (money_box_account_id)
                        REFERENCES money_box_account (money_box_account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_conciliation_resume1
                        FOREIGN KEY (resume_id)
                        REFERENCES resume (resume_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS conciliation_item (
                        conciliation_item_id INT(11)      NOT NULL AUTO_INCREMENT    COMMENT '',
                        conciliation_id      INT(11)      NOT NULL    COMMENT '',
                        amount               DOUBLE       NULL     DEFAULT NULL    COMMENT '',
                        date                 DATE         NULL     DEFAULT NULL    COMMENT '',
                        description          VARCHAR(150) NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (conciliation_item_id)        COMMENT '',
                        INDEX fk_conciliation_item_conciliation1_idx (conciliation_id ASC)        COMMENT '',
                        CONSTRAINT fk_conciliation_item_conciliation1
                        FOREIGN KEY (conciliation_id)
                        REFERENCES conciliation (conciliation_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS conciliation_item_has_resume_item (
                        conciliation_item_id INT(11) NOT NULL    COMMENT '',
                        resume_item_id       INT(11) NOT NULL    COMMENT '',
                        INDEX fk_conciliation_item_has_resume_item_conciliation_item1_idx (conciliation_item_id ASC)        COMMENT '',
                        INDEX fk_conciliation_item_has_resume_item_resume_item1_idx (resume_item_id ASC)        COMMENT '',
                        CONSTRAINT fk_conciliation_item_has_resume_item_conciliation_item1
                        FOREIGN KEY (conciliation_item_id)
                        REFERENCES conciliation_item (conciliation_item_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_conciliation_item_has_resume_item_resume_item1
                        FOREIGN KEY (resume_item_id)
                        REFERENCES resume_item (resume_item_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("CREATE TABLE IF NOT EXISTS conciliation_item_has_account_movement_item (
                        account_movement_item_id INT(11) NOT NULL    COMMENT '',
                        conciliation_item_id     INT(11) NOT NULL    COMMENT '',
                        INDEX fk_conciliation_item_has_account_movement_item_account_mov_idx (account_movement_item_id ASC)        COMMENT '',
                        INDEX fk_consolidation_item_has_account_movement_item_conciliatio_idx (conciliation_item_id ASC)        COMMENT '',
                        CONSTRAINT fk_conciliation_item_has_account_movement_item_account_movem1
                        FOREIGN KEY (account_movement_item_id)
                        REFERENCES account_movement_item (account_movement_item_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_conciliation_item_has_account_movement_item_conciliation_1
                        FOREIGN KEY (conciliation_item_id)
                        REFERENCES conciliation_item (conciliation_item_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS accounting_period (
                        accounting_period_id INT(11)                 NOT NULL AUTO_INCREMENT    COMMENT '',
                        name                 VARCHAR(150)            NULL     DEFAULT NULL    COMMENT '',
                        date_from            DATE                    NULL     DEFAULT NULL    COMMENT '',
                        date_to              DATE                    NULL     DEFAULT NULL    COMMENT '',
                        number               INT(11)                 NULL     DEFAULT NULL    COMMENT '',
                        status               ENUM('draft', 'closed') NULL     DEFAULT NULL    COMMENT '',
                        active               TINYINT(1)              NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (accounting_period_id)        COMMENT ''
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS taxes_book (
                        taxes_book_id INT(11)                 NOT NULL AUTO_INCREMENT    COMMENT '',
                        type          ENUM('sale', 'buy')     NOT NULL    COMMENT '',
                        status        ENUM('draft', 'closed') NULL     DEFAULT NULL    COMMENT '',
                        timestamp     TIMESTAMP               NULL     DEFAULT NULL    COMMENT '',
                        number        INT(11)                 NULL     DEFAULT NULL    COMMENT '',
                        company_id    INT(11)                 NULL     DEFAULT NULL    COMMENT '',
                        period        DATE                    NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (taxes_book_id)        COMMENT '',
                        INDEX ix_taxes_book_company_id (company_id ASC)        COMMENT ''
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS taxes_book_item (
                        taxes_book_item_id INT(11) NOT NULL AUTO_INCREMENT    COMMENT '',
                        page               INT(11) NOT NULL    COMMENT '',
                        taxes_book_id      INT(11) NOT NULL    COMMENT '',
                        bill_id            INT(11) NULL     DEFAULT NULL    COMMENT '',
                        provider_bill_id   INT(11) NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (taxes_book_item_id)        COMMENT '',
                        INDEX fk_taxes_book_item_taxes_book1_idx (taxes_book_id ASC)        COMMENT '',
                        INDEX fk_taxes_book_item_bill1_idx (bill_id ASC)        COMMENT '',
                        INDEX fk_taxes_book_item_provider_bill1_idx (provider_bill_id ASC)        COMMENT '',
                        CONSTRAINT fk_taxes_book_item_taxes_book1
                        FOREIGN KEY (taxes_book_id)
                        REFERENCES taxes_book (taxes_book_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_taxes_book_item_bill1
                        FOREIGN KEY (bill_id)
                        REFERENCES bill (bill_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_taxes_book_item_provider_bill1
                        FOREIGN KEY (provider_bill_id)
                        REFERENCES provider_bill (provider_bill_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS provider_bill_item (
                        provider_bill_item_id INT(11)      NOT NULL AUTO_INCREMENT    COMMENT '',
                        provider_bill_id      INT(11)      NOT NULL    COMMENT '',
                        account_id            INT(11)      NULL     DEFAULT NULL    COMMENT '',
                        description           VARCHAR(255) NULL     DEFAULT NULL    COMMENT '',
                        amount                DOUBLE       NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (provider_bill_item_id)        COMMENT '',
                        INDEX fk_provider_bill_item_provider_bill1_idx (provider_bill_id ASC)        COMMENT '',
                        INDEX fk_provider_bill_item_account1_idx (account_id ASC)        COMMENT '',
                        CONSTRAINT fk_provider_bill_item_provider_bill1
                        FOREIGN KEY (provider_bill_id)
                        REFERENCES provider_bill (provider_bill_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_provider_bill_item_account1
                        FOREIGN KEY (account_id)
                        REFERENCES account (account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        $this->execute("CREATE TABLE IF NOT EXISTS bill_has_payment (
                        bill_has_payment_id INT(11) NOT NULL AUTO_INCREMENT    COMMENT '',
                        bill_id             INT(11) NOT NULL    COMMENT '',
                        payment_id          INT(11) NOT NULL    COMMENT '',
                        amount              DOUBLE  NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (bill_has_payment_id)        COMMENT '',
                        INDEX fk_bill_has_payment_bill1_idx (bill_id ASC)        COMMENT '',
                        INDEX fk_bill_has_payment_payment1_idx (payment_id ASC)        COMMENT '',
                        CONSTRAINT fk_bill_has_payment_bill1
                        FOREIGN KEY (bill_id)
                        REFERENCES bill (bill_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_bill_has_payment_payment1
                        FOREIGN KEY (payment_id)
                        REFERENCES payment (payment_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("CREATE TABLE IF NOT EXISTS payment_item (
                        payment_item_id      INT(11)      NOT NULL AUTO_INCREMENT    COMMENT '',
                        payment_id           INT(11)      NOT NULL    COMMENT '',
                        description          VARCHAR(150) NULL     DEFAULT NULL    COMMENT '',
                        number               VARCHAR(45)  NULL     DEFAULT NULL    COMMENT '',
                        amount               DOUBLE       NULL     DEFAULT NULL    COMMENT '',
                        payment_method_id    INT(11)      NOT NULL    COMMENT '',
                        paycheck_id          INT(11)      NULL     DEFAULT NULL    COMMENT '',
                        money_box_account_id INT(11)      NULL     DEFAULT NULL    COMMENT '',
                        PRIMARY KEY (payment_item_id)        COMMENT '',
                        INDEX fk_payment_item_payment1_idx (payment_id ASC)        COMMENT '',
                        INDEX fk_payment_item_payment_method1_idx (payment_method_id ASC)        COMMENT '',
                        INDEX fk_payment_item_paycheck1_idx (paycheck_id ASC)        COMMENT '',
                        INDEX fk_payment_item_money_box_account1_idx (money_box_account_id ASC)        COMMENT '',
                        CONSTRAINT fk_payment_item_payment1
                        FOREIGN KEY (payment_id)
                        REFERENCES payment (payment_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_payment_item_payment_method1
                        FOREIGN KEY (payment_method_id)
                        REFERENCES payment_method (payment_method_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_payment_item_paycheck1
                        FOREIGN KEY (paycheck_id)
                        REFERENCES paycheck (paycheck_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION,
                        CONSTRAINT fk_payment_item_money_box_account1
                        FOREIGN KEY (money_box_account_id)
                        REFERENCES money_box_account (money_box_account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION
                    ) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci; ");

        // --- Inicio  Customer

        $this->execute("ALTER TABLE customer DROP FOREIGN KEY fk_customer_account1;");
        $this->execute("ALTER TABLE customer DROP FOREIGN KEY fk_customer_tax_condition1;");
        $this->execute("ALTER TABLE customer DROP INDEX fk_customer_address_idx;");
        $this->execute("ALTER TABLE customer CHANGE COLUMN account_id account_id INT(11) NULL COMMENT '';");
        $this->execute("ALTER TABLE customer ADD INDEX fk_customer_customer_type1_idx (tax_condition_id ASC)    COMMENT '';");
        $this->execute("ALTER TABLE customer ADD CONSTRAINT fk_customer_customer_type1
                        FOREIGN KEY (tax_condition_id)
                        REFERENCES tax_condition (tax_condition_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;
                        ALTER TABLE customer ADD CONSTRAINT fk_customer_account1
                        FOREIGN KEY (account_id)
                        REFERENCES account (account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");

        // --- Inicio Company
        $this->execute("ALTER TABLE company DROP FOREIGN KEY fk_company_tax_condition1;");
        $this->execute("ALTER TABLE company CHANGE COLUMN tax_condition_id tax_condition_id INT(11) NOT NULL COMMENT '';");

        $this->execute("ALTER TABLE company
                        ADD CONSTRAINT fk_company_tax_condition1
                        FOREIGN KEY (tax_condition_id)
                        REFERENCES tax_condition (tax_condition_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");


    // --- Inicio Point of sale
        $this->execute("ALTER TABLE point_of_sale DROP FOREIGN KEY fk_sale_point_company1;");
        $this->execute("ALTER TABLE point_of_sale DROP INDEX ix_point_of_sale_company_id;");
        $this->execute("ALTER TABLE point_of_sale CHANGE COLUMN point_of_sale_id point_of_sale_id INT(11) NOT NULL COMMENT '';");
        $this->execute("ALTER TABLE point_of_sale ADD INDEX fk_point_of_sale_company1_idx (company_id ASC)  COMMENT '';");
        $this->execute("ALTER TABLE point_of_sale
                        ADD CONSTRAINT fk_point_of_sale_company1
                        FOREIGN KEY (company_id)
                        REFERENCES company (company_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");
    // --- Inicio product
        $this->execute("ALTER TABLE product ADD COLUMN account_id INT(11) NULL DEFAULT NULL COMMENT '' AFTER uid;");
        $this->execute("ALTER TABLE product ADD INDEX fk_product_account1_idx (account_id ASC)     COMMENT '';");
        $this->execute("ALTER TABLE product
                        ADD CONSTRAINT fk_product_account1
                        FOREIGN KEY (account_id)
                        REFERENCES account (account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");

    // --- Inicio money_box
        $this->execute("ALTER TABLE money_box ADD COLUMN account_id INT(11) NULL DEFAULT NULL COMMENT '' AFTER money_box_type_id;");
        $this->execute("ALTER TABLE money_box ADD INDEX fk_money_box_account1_idx (account_id ASC)     COMMENT '';");
        $this->execute("ALTER TABLE money_box
                        ADD CONSTRAINT fk_money_box_account1
                        FOREIGN KEY (account_id)
                        REFERENCES account (account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");


    // --- Inicio money_box_account
        $this->execute("ALTER TABLE money_box_account ADD COLUMN account_id INT(11) NULL DEFAULT NULL COMMENT '' AFTER money_box_id;");
        $this->execute("ALTER TABLE money_box_account ADD COLUMN currency_id INT(11) NOT NULL  COMMENT '' AFTER account_id;");
        $this->execute("ALTER TABLE money_box_account ADD INDEX fk_money_box_account_account1_idx (account_id ASC)     COMMENT '';");
        $this->execute("ALTER TABLE money_box_account ADD INDEX fk_money_box_account_currency1_idx (currency_id ASC)     COMMENT '';");
        $this->execute("ALTER TABLE money_box_account
                        ADD CONSTRAINT fk_money_box_account_account1
                        FOREIGN KEY (account_id)
                        REFERENCES account (account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");
        $this->execute("ALTER TABLE money_box_account ADD CONSTRAINT fk_money_box_account_currency1
                        FOREIGN KEY (currency_id)
                        REFERENCES currency (currency_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");

    // --- Inicio account_config_has_account
        $this->execute("ALTER TABLE account_config_has_account DROP PRIMARY KEY;");
        $this->execute("ALTER TABLE account_config_has_account ADD COLUMN account_config_has_account_id int(11) NOT NULL PRIMARY KEY;");
        $this->execute("ALTER TABLE account_config_has_account CHANGE COLUMN account_config_has_account_id account_config_has_account_id int(11) AUTO_INCREMENT;");
        // $this->execute("ALTER TABLE account_config_has_account ADD PRIMARY KEY (account_config_has_account_id)     COMMENT '';");


    // --- Inicio account_movement
        $this->execute("ALTER TABLE account_movement CHANGE COLUMN status status ENUM('draft', 'closed') NOT NULL DEFAULT 'draft' COMMENT '';");
        $this->execute("ALTER TABLE account_movement ADD COLUMN accounting_period_id INT(11) NOT NULL COMMENT '' AFTER company_id;");
        $this->execute("ALTER TABLE account_movement ADD INDEX ix_account_movement_company_id (company_id ASC)     COMMENT '';");
        $this->execute("ALTER TABLE account_movement ADD INDEX fk_account_movement_accounting_period1_idx (accounting_period_id ASC)     COMMENT '';");
        $this->execute("ALTER TABLE account_movement
                        ADD CONSTRAINT fk_account_movement_accounting_period1
                        FOREIGN KEY (accounting_period_id)
                        REFERENCES accounting_period (accounting_period_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");

    // --- Inicio account_movement_item
        $this->execute("ALTER TABLE account_movement_item DROP FOREIGN KEY fk_account_movement_item_money_box_account1;");
        $this->execute("ALTER TABLE account_movement_item DROP COLUMN money_box_account_id;");
        $this->execute("ALTER TABLE account_movement_item ADD COLUMN status ENUM('draft', 'closed', 'conciled') NOT NULL DEFAULT 'draft' COMMENT '' AFTER credit;");

    // --- Inicio provider_payment

        $this->execute("ALTER TABLE provider_payment ADD COLUMN paycheck_id INT(11) NULL DEFAULT NULL COMMENT '' AFTER payment_method_id;");
        $this->execute("ALTER TABLE provider_payment ADD COLUMN status ENUM ('draft', 'closed', 'conciled') NOT NULL DEFAULT 'draft' COMMENT '' AFTER paycheck_id;");
        $this->execute("ALTER TABLE provider_payment ADD COLUMN money_box_account_id INT(11) NULL DEFAULT NULL COMMENT '' AFTER status;");
        $this->execute("ALTER TABLE provider_payment ADD INDEX fk_provider_payment_paycheck1_idx (paycheck_id ASC ) COMMENT '';");
        $this->execute("ALTER TABLE provider_payment ADD INDEX fk_provider_payment_money_box_account1_idx (money_box_account_id ASC)    COMMENT '';");
        $this->execute("ALTER TABLE provider_payment
                        ADD CONSTRAINT fk_provider_payment_paycheck1
                        FOREIGN KEY (paycheck_id)
                        REFERENCES paycheck (paycheck_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION");
                        $this->execute("ALTER TABLE provider_payment
                        ADD CONSTRAINT fk_provider_payment_money_box_account1
                        FOREIGN KEY (money_box_account_id)
                        REFERENCES money_box_account (money_box_account_id)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION;");

    // --- Inicio Payment

        $this->execute("ALTER TABLE payment DROP FOREIGN KEY fk_payment_payment_method1;");
        $this->execute("ALTER TABLE payment DROP FOREIGN KEY fk_payment_bill1;");
        $this->execute("ALTER TABLE payment DROP INDEX ix_payment_comany_id;");
        $this->execute("ALTER TABLE payment DROP INDEX fk_payment_payment_method1_idx;");
        $this->execute("ALTER TABLE payment DROP INDEX fk_payment_bill1_idx;");

        $this->execute("CREATE TABLE payment_bkp as select * from payment;");
        $this->execute("DROP  TABLE payment;");
        $this->execute("CREATE TABLE IF NOT EXISTS `payment` (
                        `payment_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '',
                        `amount` DOUBLE NOT NULL COMMENT '',
                        `date` DATE NULL DEFAULT NULL COMMENT '',
                        `time` TIME NULL DEFAULT NULL COMMENT '',
                        `timestamp` INT(11) NULL DEFAULT NULL COMMENT '',
                        `concept` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
                        `customer_id` INT(11) NOT NULL COMMENT '',
                        `number` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
                        `balance` DOUBLE NULL DEFAULT NULL COMMENT '',
                        `status` ENUM('draft', 'closed', 'conciled') NULL DEFAULT NULL COMMENT '',
                        `company_id` INT(11) NULL DEFAULT NULL COMMENT '',
                        PRIMARY KEY (`payment_id`)  COMMENT '',
                        INDEX `fk_payment_customer1_idx` (`customer_id` ASC)  COMMENT '',
                        CONSTRAINT `fk_payment_customer1`
                        FOREIGN KEY (`customer_id`)
                        REFERENCES `customer` (`customer_id`)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;");

        $this->execute("INSERT INTO payment_item (payment_item_id,payment_id,description,number,amount,payment_method_id)
                            SELECT NULL, payment_id, concept, number, amount, payment_method_id FROM payment_bkp;");
        $this->execute("INSERT INTO payment SELECT payment_id, amount, date, time, timestamp, concept,
                            customer_id, number, balance, 'closed',  company_id FROM payment_bkp; ");
        $this->execute("INSERT INTO bill_has_payment SELECT null, bill_id, payment_id, amount from payment_bkp;");

        $this->execute("DROP PROCEDURE IF EXISTS `sp_payment`;");
        $this->execute("
                    CREATE PROCEDURE sp_payment()
                        BEGIN
                            DECLARE v_payment_id, v_payment_method_id, v_customer_id, v_company_id, v_not_found INT DEFAULT 0;
                            DECLARE v_amount DOUBLE DEFAULT 0;
                            DECLARE v_date DATE;
                            DECLARE v_time TIME;
                            DECLARE v_timestamp INT;
                            DECLARE v_concept VARCHAR(255) DEFAULT '';

                            DECLARE c_receipt CURSOR FOR SELECT amount, date, time, concept, timestamp, customer_id,
                                                         payment_method_id, company_id FROM payment_receipt;

                            DECLARE CONTINUE HANDLER FOR NOT FOUND
                            BEGIN
                                SET v_not_found = 1;
                            END;
                            OPEN c_receipt;
                            get_receipt: LOOP
                                FETCH c_receipt
                                INTO v_amount, v_date, v_time, v_concept, v_timestamp, v_customer_id, v_payment_method_id, v_company_id;

                            IF v_not_found = 1 THEN
                                LEAVE get_receipt;
                            END IF;
                            INSERT INTO payment ( payment_id, amount,date,time,timestamp,concept, customer_id,company_id, balance, status, number)
                                     values(null, v_amount,v_date,v_time,v_timestamp,v_concept,v_customer_id,v_company_id, 0, 'closed', '');
                                SELECT LAST_INSERT_ID() INTO v_payment_id;

                            INSERT INTO payment_item (payment_item_id,payment_id,description,number,amount,payment_method_id)
                                values(null, v_payment_id, v_concept, '', v_amount, v_payment_method_id);

                        END LOOP get_receipt;
                    CLOSE c_receipt;
                    END ");
        $this->execute("call sp_payment();");
        $this->execute("DROP PROCEDURE IF EXISTS `sp_payment`;");
        $this->execute("DROP TABLE payment_bkp;");
        $this->execute("DROP TABLE payment_receipt;");
    }

    public function down()
    {
        echo "m151111_040517_paycheck_accounting cannot be reverted.\n";

        return false;
    }

}
