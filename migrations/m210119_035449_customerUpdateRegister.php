<?php

use yii\db\Migration;

/**
 * Class m210119_035449_customerUpdateRegister
 */
class m210119_035449_customerUpdateRegister extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customer_update_register', [
            'customer_update_register_id' => $this->primaryKey()->notNull(),
            'customer_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'date' => $this->integer()
        ]);

        $this->addForeignKey('fk_customer_update_register_customer', 'customer_update_register', 'customer_id', 'customer', 'customer_id');
        $this->addForeignKey('fk_customer_update_register_user', 'customer_update_register', 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customer_update_register');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210119_035449_customerUpdateRegister cannot be reverted.\n";

        return false;
    }
    */
}
