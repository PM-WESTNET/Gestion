<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 06/03/19
 * Time: 15:43
 */

use yii\db\Migration;

class m190306_154242_add_index_into_pagomiscuentas_file_has_bill_id extends Migration
{
    public function safeUp()
    {
        $this->addForeignKey('fk_pagomiscuentas_file_id', 'pagomiscuentas_file_has_bill', 'pagomiscuentas_file_id', 'pagomiscuentas_file', 'pagomiscuentas_file_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_pagomiscuentas_file_id', 'pagomiscuentas_file_has_bill');
    }
}