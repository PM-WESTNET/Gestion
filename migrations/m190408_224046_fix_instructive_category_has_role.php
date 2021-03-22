<?php

use yii\db\Migration;

/**
 * Class m190408_224046_fix_instructive_category_has_role
 */
class m190408_224046_fix_instructive_category_has_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_instructive_category_has_role_category', 'instructive_category_has_role');
        $this->addForeignKey('fk_instructive_category_has_role_category', 'instructive_category_has_role', 'instructive_category_id', 'instructive_category', 'instructive_category_id');

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
        echo "m190408_224046_fix_instructive_category_has_role cannot be reverted.\n";

        return false;
    }
    */
}
