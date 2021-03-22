<?php

use yii\db\Migration;

/**
 * Class m190828_213901_cuit2_profile_class
 */
class m190828_213901_cuit2_profile_class extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('profile_class', [
            'name' => 'cuit2',
            'data_type' => 'textInput',
            'status' => 'enabled',
            'order' => 2,
            'searchable' => 1
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('profile_class', ['name' => 'cuit2']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190828_213901_cuit2_profile_class cannot be reverted.\n";

        return false;
    }
    */
}
