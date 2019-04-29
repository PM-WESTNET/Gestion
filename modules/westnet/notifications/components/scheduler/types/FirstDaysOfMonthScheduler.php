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
}
