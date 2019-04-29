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
        $query->andWhere('from_time<="'.date('H:i:s').'"');
        $query->andWhere('to_time>="'.date('H:i:s').'"');
    }
    
}
