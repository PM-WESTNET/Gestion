<?php

use yii\db\Migration;

/**
 * Class m181008_135536_billing_company
 */
class m181008_135536_billing_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE company_has_billing (
            company_has_billing_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
            parent_company_id int NOT NULL,
            company_id int NOT NULL,
            bill_type_id int NOT NULL,
            CONSTRAINT company_has_billing_company_company_id_fk FOREIGN KEY (parent_company_id) REFERENCES company (company_id),
            CONSTRAINT company_has_billing_company_company_id_fk_2 FOREIGN KEY (company_id) REFERENCES company (company_id),
            CONSTRAINT company_has_billing_bill_type_bill_type_id_fk FOREIGN KEY (bill_type_id) REFERENCES bill_type (bill_type_id)
        );');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181008_135536_billing_company cannot be reverted.\n";

        return false;
    }
}
