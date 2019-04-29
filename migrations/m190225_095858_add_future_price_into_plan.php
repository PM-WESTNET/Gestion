<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 25/02/19
 * Time: 09:58
 */

use yii\db\Migration;

class m190225_095858_add_future_price_into_plan extends Migration
{
    public function safeUp()
    {
        $this->addColumn('product_price', 'future_final_price', $this->float());
    }

    public function safeDown()
    {
        $this->addColumn('product_price', 'future_final_price');
    }
}