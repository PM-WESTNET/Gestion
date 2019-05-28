<?php

use yii\db\Migration;

/**
 * Class m190401_181246_instructive_category_has_role
 */
class m190401_181246_instructive_category_has_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('instructive_category_has_role', [
            'instructive_category_has_role_id' => $this->primaryKey(),
            'role_code' => $this->string(255),
            'instructive_category_id' => $this->integer(11)
        ]);

        $this->addForeignKey('fk_instructive_category_has_role_category', 'instructive_category_has_role', 'instructive_category_has_role_id', 'instructive_category', 'instructive_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('instructive_category_has_role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_181246_instructive_category_has_role cannot be reverted.\n";

        return false;
    }
    */
}
