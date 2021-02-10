<?php

use yii\db\Migration;

/**
 * Class m210107_134204_node_subnet_column_not_required
 */
class m210107_134204_node_subnet_column_not_required extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('node', 'subnet', 'INT(11) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('node', 'subnet', 'INT(11) NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_134204_node_subnet_column_not_required cannot be reverted.\n";

        return false;
    }
    */
}
