<?php

use yii\db\Migration;

/**
 * Handles the creation of table `observations`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `customer`
 */
class m220516_154138_create_observations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('observations', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'observation' => $this->string(),
            'date' => $this->date(),
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-observations-author_id',
            'observations',
            'author_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-observations-author_id',
            'observations',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `customer_id`
        $this->createIndex(
            'idx-observations-customer_id',
            'observations',
            'customer_id'
        );

        // add foreign key for table `customer`
        $this->addForeignKey(
            'fk-observations-customer_id',
            'observations',
            'customer_id',
            'customer',
            'customer_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-observations-author_id',
            'observations'
        );

        // drops index for column `author_id`
        $this->dropIndex(
            'idx-observations-author_id',
            'observations'
        );

        // drops foreign key for table `customer`
        $this->dropForeignKey(
            'fk-observations-customer_id',
            'observations'
        );

        // drops index for column `customer_id`
        $this->dropIndex(
            'idx-observations-customer_id',
            'observations'
        );

        $this->dropTable('observations');
    }
}
