<?php

use yii\db\Migration;

/**
 * Class m190625_154904_direct_debit_export_type_column
 */
class m190625_154904_direct_debit_export_type_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('direct_debit_export', 'type', 'ENUM("own", "other") NULL DEFAULT "own"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('direct_debit_export', 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_154904_direct_debit_export_type_column cannot be reverted.\n";

        return false;
    }
    */
}
