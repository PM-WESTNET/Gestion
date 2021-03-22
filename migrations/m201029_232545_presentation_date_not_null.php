<?php

use yii\db\Migration;

/**
 * Class m201029_232545_presentation_date_not_null
 */
class m201029_232545_presentation_date_not_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('firstdata_import', 'presentation_date', 'INTEGER NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('firstdata_import', 'presentation_date', 'INTEGER NOT NULL');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201029_232545_presentation_date_not_null cannot be reverted.\n";

        return false;
    }
    */
}
