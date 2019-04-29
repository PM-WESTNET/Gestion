<?php

use yii\db\Migration;

/**
 * Class m180827_152212_text_fields
 */
class m180827_152212_text_fields extends Migration
{

    public function init()
    {
        $this->db = 'dblog';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('log', 'post', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180827_152212_text_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180827_152212_text_fields cannot be reverted.\n";

        return false;
    }
    */
}