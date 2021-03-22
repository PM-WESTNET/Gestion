<?php

use yii\db\Migration;

/**
 * Class m210104_174227_access_point_id_column_connection_table
 */
class m210104_174227_access_point_id_column_connection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('connection', 'access_point_id', 'INT NULL');
        $this->addForeignKey('fk_access_point_connection', 'connection', 'access_point_id', 'access_point', 'access_point_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('connection', 'access_point_id');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210104_174227_access_point_id_column_connection_table cannot be reverted.\n";

        return false;
    }
    */
}
