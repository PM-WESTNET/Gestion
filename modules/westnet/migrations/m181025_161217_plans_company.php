<?php

use yii\db\Migration;

/**
 * Class m181025_161217_plans_company
 */
class m181025_161217_plans_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE product ADD company_id int NULL;');
        $this->execute('ALTER TABLE product ADD show_in_ads int NULL;');
        $this->execute('ALTER TABLE product ADD ads_name varchar (15) null ;');
        $this->execute('ALTER TABLE product ADD CONSTRAINT product_company_company_id_fk FOREIGN KEY (company_id) REFERENCES company (company_id);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181025_161217_plans_company cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181025_161217_plans_company cannot be reverted.\n";

        return false;
    }
    */
}
