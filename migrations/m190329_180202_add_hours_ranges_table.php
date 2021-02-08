<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 29/03/19
 * Time: 18:05
 */

use yii\db\Migration;

class m190329_180202_add_hours_ranges_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('hour_range', [
            'hour_range_id' => $this->primaryKey(),
            'from' => $this->string(),
            'to' => $this->string()
        ]);

        $this->createTable('customer_has_hour_range', [
            'customer_has_hour_range_id' => $this->primaryKey(),
            'customer_id' => $this->integer(),
            'hour_range_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_customer_has_hour_range_customer_id', 'customer_has_hour_range', 'customer_id', 'customer', 'customer_id');
        $this->addForeignKey('fk_customer_has_hour_range_hour_range_id', 'customer_has_hour_range', 'hour_range_id', 'hour_range', 'hour_range_id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_customer_has_hour_range_hour_range_id', 'customer_has_hour_range');
        $this->dropForeignKey('fk_customer_has_hour_range_customer_id', 'customer_has_hour_range');

        $this->dropTable('customer_has_hour_range');
        $this->dropTable('hour_range');
    }
}