<?php

use yii\db\Migration;

/**
 * Class m190329_194632_instructive_tables
 */
class m190329_194632_instructive_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('instructive_category', [
            'instructive_category_id' => $this->primaryKey(),
            'name' => $this->string(45)->notNull(),
            'status' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('instructive', [
            'instructive_id' => $this->primaryKey(),
            'name' => $this->string(45)->notNull(),
            'summary' => $this->string(255),
            'content' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'instructive_category_id' => $this->integer()->notNull()
        ]);

        $this->addForeignKey('fk_instructive_instructive_category', 'instructive', 'instructive_category_id', 'instructive_category', 'instructive_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //$this->dropTable('instructive');
        $this->dropTable('instructive_category');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190329_194632_instructive_tables cannot be reverted.\n";

        return false;
    }
    */
}
