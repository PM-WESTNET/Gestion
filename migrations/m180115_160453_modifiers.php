<?php

use yii\db\Migration;

/**
 * Class m180115_160453_modifiers
 */
class m180115_160453_modifiers extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Bill
        $this->execute('ALTER TABLE bill ADD created_at int null;');
        $this->execute('ALTER TABLE bill ADD updated_at int null;');
        $this->execute('ALTER TABLE bill ADD creator_user_id int null;');
        $this->execute('ALTER TABLE bill ADD updater_user_id int null;');

        // Bill_Detail
        $this->execute('ALTER TABLE bill_detail ADD created_at int null;');
        $this->execute('ALTER TABLE bill_detail ADD updated_at int null;');
        $this->execute('ALTER TABLE bill_detail ADD creator_user_id int null;');
        $this->execute('ALTER TABLE bill_detail ADD updater_user_id int null;');

        // provider_bill
        $this->execute('ALTER TABLE provider_bill ADD created_at int null;');
        $this->execute('ALTER TABLE provider_bill ADD updated_at int null;');
        $this->execute('ALTER TABLE provider_bill ADD creator_user_id int null;');
        $this->execute('ALTER TABLE provider_bill ADD updater_user_id int null;');


        // provider_bill_item
        $this->execute('ALTER TABLE provider_bill_item ADD created_at int null;');
        $this->execute('ALTER TABLE provider_bill_item ADD updated_at int null;');
        $this->execute('ALTER TABLE provider_bill_item ADD creator_user_id int null;');
        $this->execute('ALTER TABLE provider_bill_item ADD updater_user_id int null;');


        // account_movement
        $this->execute('ALTER TABLE account_movement ADD created_at int null;');
        $this->execute('ALTER TABLE account_movement ADD updated_at int null;');
        $this->execute('ALTER TABLE account_movement ADD creator_user_id int null;');
        $this->execute('ALTER TABLE account_movement ADD updater_user_id int null;');


        // account_movement_detail
        $this->execute('ALTER TABLE account_movement_item ADD created_at int null;');
        $this->execute('ALTER TABLE account_movement_item ADD updated_at int null;');
        $this->execute('ALTER TABLE account_movement_item ADD creator_user_id int null;');
        $this->execute('ALTER TABLE account_movement_item ADD updater_user_id int null;');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180115_160453_modifiers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180115_160453_modifiers cannot be reverted.\n";

        return false;
    }
    */
}
