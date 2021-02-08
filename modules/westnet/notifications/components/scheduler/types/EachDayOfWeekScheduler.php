<?php

namespace app\modules\westnet\notifications\components\scheduler\types;

use yii\base\Component;

/**
 * Description of DailyScheduler
 *
 * @author martin
 */
class EachDayOfWeekScheduler extends Component implements SchedulerInterface{
    
    static $selectOneDay = false;
    static $selectDays = true;
    static $selectDates = true;
    
    public static function name()
    {
        return 'Días seleccionados, cada semana';
    }
    
    public static function description()
    {
        return 'Esta notificación será enviada cada día seleccionado de cada semana.';
    }
    
    public function mergeQuery(&$query)
    {
        $query->andWhere(['scheduler' => $this->className()]);
        
        $day = strtolower(date('l'));
        $query->andWhere([$day => true]);
    }

    /**
     * Devuelve la próxima fecha de envío de una notificación
     */
    public function getNextSend($notification)
    {
        $next_dates = [];
        $date = (new \DateTime('now'))->format('d-m-Y');

        if ((new \DateTime($notification->to_date)) >= (new \DateTime($date))) {

            //Se tiene en cuenta la fecha desde en la calendarización
            if ((new \DateTime($notification->from_date)) > (new \DateTime($date))) {
                $date = (new \DateTime($notification->from_date))->format('d-m-Y');
            }

            if ($notification->monday) { array_push($next_dates, (new \DateTime($date))->modify('next monday')->format('d-m-Y'));};
            if ($notification->tuesday) { array_push($next_dates, (new \DateTime($date))->modify('next tuesday')->format('d-m-Y'));};
            if ($notification->wednesday) { array_push($next_dates, (new \DateTime($date))->modify('next wednesday')->format('d-m-Y'));};
            if ($notification->thursday) { array_push($next_dates, (new \DateTime($date))->modify('next thursday')->format('d-m-Y'));};
            if ($notification->friday) {array_push($next_dates, (new \DateTime($date))->modify('next friday')->format('d-m-Y'));};
            if ($notification->saturday) {array_push($next_dates, (new \DateTime($date))->modify('next saturday')->format('d-m-Y'));};
            if ($notification->sunday) {array_push($next_dates, (new \DateTime($date))->modify('next sunday')->format('d-m-Y'));};

            //Se ordena el array con las fechas de menor a mayor
            usort($next_dates, function ($a, $b) {
                return strtotime($a) - strtotime($b);
            });

            if (!empty($next_dates)) {
                return $next_dates[0];
            }
        }

        return '';
    }
}
