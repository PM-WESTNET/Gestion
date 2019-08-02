<?php

use yii\db\Migration;

/**
 * Class m190702_195217_create_timestamp_connection_force_historial
 */
class m190702_195217_create_timestamp_connection_force_historial extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('connection_forced_historial', 'create_timestamp', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('connection_forced_historial', 'create_timestamp');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190702_195217_create_timestamp_connection_force_historial cannot be reverted.\n";

        return false;
    }
    */
}
