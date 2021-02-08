<?php

use yii\db\Migration;

/**
 * Class m190731_151316_header_file_can_be_null
 */
class m190731_151316_header_file_can_be_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('pago_facil_transmition_file', 'header_file', 'VARCHAR(256) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('pago_facil_transmition_file', 'header_file', 'VARCHAR(256) NOT NULL DEFAULT ""');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190731_151316_header_file_can_be_null cannot be reverted.\n";

        return false;
    }
    */
}
