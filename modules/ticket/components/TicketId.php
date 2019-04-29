<?php

namespace app\modules\ticket\components;

use Yii;
use app\modules\ticket\models\Ticket;
use app\modules\ticket\models\Color;

/**
 * Description of TicketColor
 *
 * @author smaldonado
 */
class TicketId {

    public $userModelClass;
    public $userModelId;
    private $colors = [
        "#2774E8", //azul
        "#1CFF61", //verde
        "#FFF238", //amarillo
        "#F56D1F", //naranja
        '#FF2C13', //rojo
        "#691CF5", //violeta
    ];
    private $ranges = [
        0 => [
            'from' => 0,
            'to' => 2,
        ],
        1 => [
            'from' => 3,
            'to' => 5,
        ],
        2 => [
            'from' => 6,
            'to' => 8,
        ],
        3 => [
            'from' => 9,
            'to' => 12,
        ],
        4 => [
            'from' => 13,
            'to' => 15,
        ],
        5 => [
            'from' => 16,
            'to' => 100000,
        ],
    ];

    /**
     * Initializes this component
     */
    public function init() {

        $ticketModule = Yii::$app->getModule('ticket');

        if (isset($ticketModule->params['user']['class']))
            $this->userModelClass = $ticketModule->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset($ticketModule->params['user']['idAttribute']))
            $this->userModelId = $ticketModule->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';
    }

    /**
     * Assigns a color depending on the remaining open tickets for $client_id
     * @param integer $client_id
     * @return string
     */
    public function getColor($customer_id) {


        //Calculating a key for returning a color
        $key = 0;
        $maxKey = count($this->findAllColors());

        //Obtaining how many open tickets there are for $client_id
        $openTicketsCount = Ticket::find()->where([
                    'customer_id' => $customer_id,
                ])->isStatusOpen()->count();

        if ($openTicketsCount < $maxKey) {
            $key = $openTicketsCount;
        } else {
            $key = $openTicketsCount % $maxKey;
        }

        $color = $this->findColor($key);

        if (!empty($color)) {
            return $color;
        }
    }

    /**
     * Returns a color depending on number of observations
     * @param type $observations
     */
    public function getColorByObservations($observations = []) {

        if (!empty($observations)) {
            $count = count($observations);

            foreach ($this->ranges as $keyRange => $range) {
                if ($count >= $range['from'] && $count <= $range['to']) {
                    return $this->findColor($keyRange);
                    break;
                }
            }
        } else {
            return $this->findColor(0);
        }
    }

    /**
     * Assigns a number depending on the remaining open tickets for $client_id
     * @param type $client_id
     * @return type
     */
    public function assignNumber($customer_id) {

        //Obtaining how many open tickets there are for $client_id
        $openTicketsCount = Ticket::find()->where([
                    'customer_id' => $customer_id,
                ])->isStatusOpen()->count();

        return $openTicketsCount + 1;
    }

    /**
     * Obtain all colors from DB
     * @return type
     */
    private function findAllColors() {
        return Color::find()->all();
    }

    /**
     * Obtains a color from DB
     * @return array
     */
    private function findColor($order) {
        return Color::find()->where([
                    'order' => $order
                ])->one();
    }

}
