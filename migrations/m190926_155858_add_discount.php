<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;
use app\modules\ticket\models\TicketManagement;
use app\modules\ticket\models\Observation;

class m190926_155858_add_discount extends Migration
{
    public function safeUp()
    {
        $this->insert('discount', [
            'name' => '25% de descuento por recomendado',
            'status' => 'enabled',
            'type' => 'percentage',
            'value' => 25,
            'from_date' => (new \DateTime('now'))->format('Y-m-d'),
            'to_date' => (new \DateTime('now'))->modify('+1 year')->format('Y-m-d'),
            'periods' => 1,
            'apply_to' => 'product',
            'value_from' => 'plan',
            'referenced' => 1,
            'persistent' => 1
        ]);
    }

    public function safeDown()
    {
        $this->delete('discount', ['name' => '25% de descuento por recomendado']);
    }
}
