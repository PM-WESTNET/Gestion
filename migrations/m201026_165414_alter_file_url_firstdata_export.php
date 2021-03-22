<?php

use yii\db\Migration;

/**
 * Class m201026_165414_alter_file_url_firstdata_export
 */
class m201026_165414_alter_file_url_firstdata_export extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('firstdata_export', 'file_url', 'VARCHAR(255) NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('firstdata_export', 'file_url', 'VARCHAR(255) NOT NULL');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_165414_alter_file_url_firstdata_export cannot be reverted.\n";

        return false;
    }
    */
}
