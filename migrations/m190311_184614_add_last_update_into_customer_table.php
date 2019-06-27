<?php

use yii\db\Migration;

/**
 * Class m190311_184614_add_last_update_into_customer_table
 */
class m190311_184614_add_last_update_into_customer_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('customer', 'last_update', $this->date()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'last_update');
    }
}
