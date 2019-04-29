<?php

namespace app\modules\westnet\notifications\components\scheduler;

use Yii;
use yii\base\Component;
use app\components\helpers\ClassFinderHelper;

/**
 * Description of Scheduler
 *
 * @author mmoyano
 */
class Scheduler extends Component{
    
    public static function getSchedulersForSelect()
    {
        $classNames = self::getSchedulerClasses();
        
        $objects = [];
        foreach($classNames as $class){
            $objects[] = new $class;
        }
        
        $select = [];
        foreach($objects as $obj){
            $select[$obj->className()] = $obj->name();
        }
        
        return $select;
    }
    
    public static function getSchedulerClasses()
    {
        $classes = ClassFinderHelper::findClasses([
            '@app/modules/westnet/notifications/components/scheduler/types'
        ]);
        
        return $classes;
    }
    
    public static function getSchedulerObjects()
    {
        $classes = ClassFinderHelper::findClasses([
            '@app/modules/westnet/notifications/components/scheduler/types'
        ]);
        
        $objects = [];
        foreach($classes as $class){
            $objects[] = self::getSchedulerObject($class);
        }
        
        return $objects;
    }
    
    public static function getSchedulerObject($class)
    {
        return new $class;
    }
    
}
