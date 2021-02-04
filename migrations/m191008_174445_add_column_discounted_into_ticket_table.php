<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;
use app\modules\ticket\models\TicketManagement;
use app\modules\ticket\models\Observation;

class m191008_174445_add_column_discounted_into_ticket_table extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('ticket', 'discounted', $this->boolean());
    }

    public function safeDown()
    {
        $this->dropColumn('ticket', 'discounted');
    }
}
