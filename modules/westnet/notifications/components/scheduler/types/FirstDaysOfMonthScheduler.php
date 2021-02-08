<?php

namespace app\modules\westnet\notifications\components\scheduler\types;

use yii\base\Component;

/**
 * Description of DailyScheduler
 *
 * @author martin
 */
class FirstDaysOfMonthScheduler extends Component implements SchedulerInterface{
    
    static $selectOneDay = false;
    static $selectDays = true;
    static $selectDates = true;
    
    public static function name()
    {
        return 'Primeros días seleccionados del mes, cada mes';
    }
    
    public static function description()
    {
        return 'Esta notificación será enviada los días seleccionados de la primer semana de cada mes.';
    }
    
    public function mergeQuery(&$query)
    {
        $query->andWhere(['scheduler' => $this->className()]);
        
        //Si no es el primer dia del mes, no hacemos nada
        if(date('d') > 7){
            $query->andWhere('1=0');        
        }else{
            $day = strtolower(date('l'));
            $query->andWhere([$day => true]);
        }
    }

    /**
     * Devuelve la próxima fecha de envio de una notificacion
     */
    public function getNextSend($notification)
    {
        $date = (new  \DateTime('now'));
        $last_day_of_the_first_week = (new \DateTime())->modify('first day of this month')->modify('+7 days');

        if($date > $last_day_of_the_first_week){
            $date = $date->modify('first day of next month');
        }

        $next_dates = [];
        if($notification->monday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next monday')->format('d-m-Y')); };
        if($notification->tuesday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next tuesday')->format('d-m-Y')); };
        if($notification->wednesday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next wednesday')->format('d-m-Y')); };
        if($notification->thursday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next thursday')->format('d-m-Y')); };
        if($notification->friday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next friday')->format('d-m-Y')); };
        if($notification->saturday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next saturday')->format('d-m-Y')); };
        if($notification->sunday){ array_push($next_dates, (new \DateTime($date->format('Y-m-d')))->modify('next sunday')->format('d-m-Y')); };

        //Se ordena el array con las fechas de menor a mayor
        usort($next_dates, function ($a, $b) {return strtotime($a) - strtotime($b);});

        if(!empty($next_dates)) {
            return $next_dates[0];
        }

        return '';
    }
}
