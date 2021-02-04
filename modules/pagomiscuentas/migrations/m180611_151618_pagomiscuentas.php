<?php

use yii\db\Migration;

/**
 * Class m180611_151618_pagomiscuentas
 */
class m180611_151618_pagomiscuentas extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE pagomiscuentas_file (
                pagomiscuentas_file_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                date date NOT NULL,
                file varchar(256),
                path varchar(256),
                company_id int(11) NOT NULL,
                type enum('bill', 'payment') ,
                status enum('draft', 'closed') ,
                CONSTRAINT pagomiscuentas_file_company_company_id_fk FOREIGN KEY (company_id) REFERENCES company (company_id) ); ");

        $this->execute("CREATE TABLE pagomiscuentas_file_has_bill (
                pagomiscuentas_file_has_bill_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                pagomiscuentas_file_id int NOT NULL,
                bill_id int NOT NULL,
                CONSTRAINT pagomiscuentas_file_has_bill_fk FOREIGN KEY (bill_id) REFERENCES bill (bill_id) ); ");

        $this->execute("CREATE TABLE pagomiscuentas_file_has_payment (
                pagomiscuentas_file_has_payment_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                pagomiscuentas_file_id int NOT NULL,
                payment_id int NOT NULL,
                CONSTRAINT pagomiscuentas_file_has_payment_fk FOREIGN KEY (payment_id) REFERENCES payment (payment_id)); ");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }
}
