<?php

use yii\db\Migration;

/**
 * Class m180504_163741_task_slug
 */
class m180504_163741_task_slug extends Migration
{
    public function init() {
        $this->db = 'dbagenda';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE task MODIFY slug varchar(255);');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180504_163741_task_slug cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_163741_task_slug cannot be reverted.\n";

        return false;
    }
    */
}
