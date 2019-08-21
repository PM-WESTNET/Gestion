<?php

use yii\db\Migration;

/**
 * Class m190821_160334_instalation_schedule_default_value
 */
class m190821_160334_instalation_schedule_default_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('contract', 'instalation_schedule', 'enum("in the morning", "in the afternoon", "all day") NOT NULL DEFAULT "all day"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('contract', 'instalation_schedule', 'enum("in the morning", "in the afternoon", "all day") NULL DEFAULT NULL');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190821_160334_instalation_schedule_default_value cannot be reverted.\n";

        return false;
    }
    */
}
