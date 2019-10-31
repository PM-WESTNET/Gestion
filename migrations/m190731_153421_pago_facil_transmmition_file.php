<?php

use yii\db\Migration;

/**
 * Class m190731_153421_pago_facil_transmmition_file
 */
class m190731_153421_pago_facil_transmmition_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('pago_facil_transmition_file', 'total', 'DOUBLE NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('pago_facil_transmition_file', 'total', 'DOUBLE NOT NULL DEFAULT ""');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190731_153421_pago_facil_transmmition_file cannot be reverted.\n";

        return false;
    }
    */
}
