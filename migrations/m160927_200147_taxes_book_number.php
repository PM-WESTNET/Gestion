<?php

use yii\db\Migration;

class m160927_200147_taxes_book_number extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE taxes_book MODIFY number VARCHAR(20);');

    }

    public function down()
    {
        echo "m160927_200147_taxes_book_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
