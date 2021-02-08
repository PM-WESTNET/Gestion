<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;
use app\modules\ticket\models\TicketManagement;
use app\modules\ticket\models\Observation;

class m190913_102424_add_observation_column_into_ticket_management extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('observation', 'ticket_management_id', $this->integer());

        $this->addForeignKey('fk_observation_ticket_management_id', 'observation', 'ticket_management_id', 'ticket_management', 'ticket_management_id');

        foreach (TicketManagement::find()->all() as $tm) {
            $observation = Observation::find()->where(['ticket_id' => $tm->ticket_id])->andWhere(['ticket_management_id' => null])->one();
            if($observation) {
                $observation->updateAttributes(['ticket_management_id' => $tm->ticket_management_id]);
            }
        }
    }

    public function safeDown()
    {
        $this->dropColumn('observation', 'ticket_management_id');

        Observation::updateAll(['ticket_management_id' => null]);
    }
}
