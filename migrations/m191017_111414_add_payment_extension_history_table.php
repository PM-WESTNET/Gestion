<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\ticket\models\Status;

class m191017_111414_add_payment_extension_history_table extends Migration
{
    public function safeUp()
    {
       $this->createTable('payment_extension_history', [
           'payment_extension_history_id' => $this->primaryKey(),
           'from' => "ENUM('app', 'ivr')",
           'customer_id' => $this->integer(),
           'date' => $this->string(),
           'created_at' => $this->integer(),
       ]);

       $this->addForeignKey('fk_payment_extension_history', 'payment_extension_history', 'customer_id', 'customer', 'customer_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_payment_extension_history', 'payment_extension_history');

        $this->dropTable('payment_extension_history');
    }
}
