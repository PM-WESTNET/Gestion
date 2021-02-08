<?php

namespace app\modules\westnet\notifications\components\scheduler\types;

use yii\base\Component;
use app\modules\westnet\notifications\models\search\NotificationSearch;

/**
 * Description of PeriodScheduler
 *
 * @author martin
 */
class PeriodScheduler extends Component implements SchedulerInterface{
    
    static $selectOneDay = false;
    static $selectDays = false;
    static $selectDates = true;
    
    public static function name()
    {
        return 'Envío por día desde fecha de inicio hasta fecha de fin';
    }
    
    public static function description()
    {
        return 'Esta notificación será enviada una vez por día entre las fechas elegidas.';
    }
    
    public function mergeQuery(&$query)
    {
        $query->andWhere(['scheduler' => $this->className()]);
        
        $query->andWhere('from_date<="'.date('Y-m-d').'"');
        $query->andWhere('to_date>="'.date('Y-m-d').'"');
    }

    /**
     * Devuelve la próxima fecha de envio de una notificacion
     */
    public function getNextSend($notification)
    {

        $date = (new \DateTime($notification->from_date));
        $today = (new \DateTime('now'));
        $from_date = (new \DateTime($notification->from_date));
        $to_date = (new \DateTime($notification->to_date));

        if($today <= $to_date) {
            if($today > $from_date){
                $date = $today;
            }

            return $date->modify('+1 days')->format('d-m-Y');
        }

        return '';
    }
}
