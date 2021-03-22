<?php

use yii\db\Migration;

/**
 * Class m181003_163147_node_code_ecopago
 */
class m181003_163147_node_code_ecopago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE node DROP COLUMN code;');
        $this->execute('ALTER TABLE node ADD COLUMN has_ecopago_close INT;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181003_163147_node_code_ecopago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181003_163147_node_code_ecopago cannot be reverted.\n";

        return false;
    }
    */
}
