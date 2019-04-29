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
}
