<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;
use app\modules\ticket\models\TicketManagement;
use app\modules\ticket\models\Observation;

class m190917_180808_add_column_into_discount_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('discount', 'persistent', $this->boolean());
    }

    public function safeDown()
    {
        $this->dropColumn('discount', 'persistent');
    }
}
