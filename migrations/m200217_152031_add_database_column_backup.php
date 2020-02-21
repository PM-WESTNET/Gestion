<?php

use yii\db\Migration;

/**
 * Class m200217_152031_add_database_column_backup
 */
class m200217_152031_add_database_column_backup extends Migration
{

    public function init()
    {
        $this->db= 'dbbackups';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('backup', 'database', 'VARCHAR(45) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('backup', 'database');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_152031_add_database_column_backup cannot be reverted.\n";

        return false;
    }
    */
}
