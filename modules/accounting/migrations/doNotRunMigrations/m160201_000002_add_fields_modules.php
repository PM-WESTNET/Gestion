<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_000002_add_fields_modules extends Migration
{
    public function up()
    {
        // Agrego a customer el account_id
        $this->execute("ALTER TABLE customer ADD account_id INT NULL;");
        $this->execute("ALTER TABLE customer ADD CONSTRAINT customer_account_account_id_fk FOREIGN KEY (account_id) REFERENCES account (account_id);");

        $this->execute("ALTER TABLE product ADD account_id INT NULL;");
        $this->execute("ALTER TABLE product ADD CONSTRAINT product_account_account_id_fk FOREIGN KEY (account_id) REFERENCES account (account_id);");

    }

    public function down()
    {
        echo "m150802_184839_add_fields_modules cannot be reverted.\n";

        return false;
    }
}
