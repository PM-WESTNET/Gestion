<?php

use yii\db\Migration;

/**
 * Class m190715_151853_add_oauth_client
 */
class m190715_151853_add_oauth_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_151853_add_oauth_client cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_151853_add_oauth_client cannot be reverted.\n";

        return false;
    }
    */
}
