<?php

use yii\db\Migration;
use app\modules\sale\models\Category;

class m190814_173434_add_product_categories extends Migration
{
    public function safeUp()
    {
        $this->insert('category', [
            'name' => 'Plan fibra',
            'status' => 'enabled',
            'system' => 'plan-fibra'
        ]);

        $this->insert('category', [
            'name' => 'Plan wifi',
            'status' => 'enabled',
            'system' => 'plan-wifi'
        ]);
    }


    public function safeDown()
    {
        $this->delete('category', ['system' => 'plan-wifi']);

        $this->delete('category', ['system' => 'plan-fibra']);
    }
}
