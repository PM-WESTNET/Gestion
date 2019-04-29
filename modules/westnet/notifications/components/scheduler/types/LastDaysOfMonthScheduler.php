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
}
