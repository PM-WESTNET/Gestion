<?php

namespace app\modules\westnet\notifications\components\scheduler\types;

use yii\base\Component;

/**
 * Description of DailyScheduler
 *
 * @author martin
 */
class LastDaysOfMonthScheduler extends Component implements SchedulerInterface{
    
    static $selectOneDay = false;
    static $selectDays = true;
    static $selectDates = true;
    
    public static function name()
    {
        return 'Últimos días seleccionados del mes, cada mes';
    }
    
    public static function description()
    {
        return 'Esta notificación será enviada los días seleccionado en la última semana de cada mes.';
    }
    
    public function mergeQuery(&$query)
    {
        $query->andWhere(['scheduler' => $this->className()]);
        
        //Si no es la ultima semana del mes, no hacemos nada
        if(date('d') < (date('t') - 7)){
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
        $date = (new \DateTime('now'))->modify('last day of this month')->modify('-7 days');

        if((new  \DateTime('now')) > (new \DateTime('now'))->modify('last day of this month')){
            $date = (new \DateTime('now'))->modify('last day of next month')->modify('-7 days');
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
