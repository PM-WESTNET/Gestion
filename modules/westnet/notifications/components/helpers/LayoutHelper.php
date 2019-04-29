<?php
namespace app\modules\westnet\notifications\components\helpers;

use Yii;
use yii\base\Component;
use app\modules\westnet\notifications\NotificationsModule;

/**
 * Description of LayoutHelper
 *
 * @author mmoyano
 */
class LayoutHelper extends Component{
    
    /**
     * Devuelve un array con los layouts disponibles en:
     *  app/modules/westnet/notifications/body/layouts
     * Las claves son los nombres del archivo sin .php; los valores son los
     * nombres del archivo con i18n aplicado.
     * @return array
     */
    public static function getLayouts()
    {
        $path = Yii::getAlias('@app/modules/westnet/notifications/body/layouts');
        $layouts = scandir($path);
        
        //Quitamos . y ..
        $layouts = array_diff($layouts, ['.','..']);
        
        //Quitamos .php
        $layouts = array_map(function($item){ return str_replace('.php', '', $item); }, $layouts);
        
        //i18n
        $names = array_map(function($item){ return NotificationsModule::t('app', $item); }, $layouts);
        
        return array_combine($layouts, $names);
    }
   
    /**
     * Devuelve el path completo al layout pasado como parametro
     * @param string $layout
     * @return string
     */
    public static function getLayoutPath($layout)
    {
        $path = Yii::getAlias(self::getLayoutAlias($layout));
        return $path;
    }
   
    /**
     * Devuelve el path completo al layout pasado como parametro
     * @param string $layout
     * @return string
     */
    public static function getLayoutAlias($layout)
    {
        return '@app/modules/westnet/notifications/body/layouts/'.$layout;
        
    }
    
}
