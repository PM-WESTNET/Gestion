<?php

use yii\db\Migration;

/**
 * Class m200211_192541_add_phones_and_emails_app_failed_register
 */
class m200211_192541_add_phones_and_emails_app_failed_register extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('app_failed_register', 'phone2', 'VARCHAR(45) NULL');
        $this->addColumn('app_failed_register', 'phone3', 'VARCHAR(45) NULL');
        $this->addColumn('app_failed_register', 'phone4', 'VARCHAR(45) NULL');
        $this->addColumn('app_failed_register', 'email2', 'VARCHAR(255) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('app_failed_register', 'phone2');
        $this->dropColumn('app_failed_register', 'phone3');
        $this->dropColumn('app_failed_register', 'phone4');
        $this->dropColumn('app_failed_register', 'email2');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200211_192541_add_phones_and_emails_app_failed_register cannot be reverted.\n";

        return false;
    }
    */
}
