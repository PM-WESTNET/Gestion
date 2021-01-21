<?php

use yii\db\Migration;

/**
 * Class m210120_185611_mac_address_connection_table
 */
class m210120_185611_mac_address_connection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('connection', 'mac_address', "VARCHAR(255) NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('connection', 'mac_address');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210120_185611_mac_address_connection_table cannot be reverted.\n";

        return false;
    }
    */
}
