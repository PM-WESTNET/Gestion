<?php

use yii\db\Migration;

class m161110_134806_provider_payments extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE provider_payment_item
                (
               provider_payment_item_id INT PRIMARY KEY AUTO_INCREMENT,
              provider_payment_id INT NOT NULL,
              description VARCHAR(255) DEFAULT '',
              number VARCHAR(45) DEFAULT '',
              amount DOUBLE,
              payment_method_id INT,
              paycheck_id INT null,
              money_box_account_id INT null,
              CONSTRAINT provider_payment_item_provider_payment_provider_payment_id_fk FOREIGN KEY (provider_payment_id) REFERENCES provider_payment (provider_payment_id),
              CONSTRAINT provider_payment_item_payment_method_payment_method_id_fk FOREIGN KEY (payment_method_id) REFERENCES payment_method (payment_method_id),
              CONSTRAINT provider_payment_item_paycheck_paycheck_id_fk FOREIGN KEY (paycheck_id) REFERENCES paycheck (paycheck_id),
              CONSTRAINT provider_payment_item_money_box_account_money_box_account_id_fk FOREIGN KEY (money_box_account_id) REFERENCES money_box_account (money_box_account_id)
            ) ENGINE=INNODB DEFAULT CHARACTER SET = utf8; ");

        $this->execute("insert into provider_payment_item (provider_payment_item_id,provider_payment_id,description,number,amount,payment_method_id,paycheck_id,money_box_account_id)
            select null, provider_payment_id, description, number, amount, payment_method_id, paycheck_id, money_box_account_id
            from provider_payment");

        $this->execute("ALTER TABLE provider_payment DROP FOREIGN KEY fk_provider_payment_payment_method1;");
        $this->execute("ALTER TABLE provider_payment DROP FOREIGN KEY fk_provider_payment_paycheck1;");
        $this->execute("ALTER TABLE provider_payment DROP FOREIGN KEY fk_provider_payment_money_box_account1;");
        $this->execute("DROP INDEX fk_provider_payment_money_box_account1_idx ON provider_payment;");
        $this->execute("DROP INDEX fk_provider_payment_paycheck1_idx ON provider_payment;");
        $this->execute("DROP INDEX fk_provider_payment_payment_method1_idx ON provider_payment;");
        $this->execute("ALTER TABLE provider_payment DROP number;");
        $this->execute("ALTER TABLE provider_payment DROP payment_method_id;");
        $this->execute("ALTER TABLE provider_payment DROP paycheck_id;");
        $this->execute("ALTER TABLE provider_payment DROP money_box_account_id;");

    }

    public function down()
    {
        echo "m161110_134806_provider_payments cannot be reverted.\n";

        return false;
    }

}
