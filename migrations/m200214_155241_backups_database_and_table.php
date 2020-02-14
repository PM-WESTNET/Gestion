<?php

use yii\db\Migration;

/**
 * Class m200214_155241_backups_database_and_table
 */
class m200214_155241_backups_database_and_table extends Migration
{
    public function init()
    {
        $this->db='dbbackups';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('backup', [
           'backup_id' => $this->primaryKey(),
           'init_timestamp' => $this->integer()->notNull(),
           'finish_timestamp' => $this->integer(),
           'status' => 'ENUM("in_process","success","error")',
           'description' => $this->text()
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_155241_backups_database_and_table cannot be reverted.\n";

        return false;
    }
    */
}
