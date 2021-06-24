<?php

namespace app\components\helpers;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Url;
/**
 * Para renderizar un link y verificar al mismo tiempo si el usuario actual
 * tiene permisos para acceder.
 *
 * @author mmoyano
 */
class UserA extends Component{
    
    public static function a($text, $url, $options = [])
    {
        
        if($url === null || User::canRoute(static::normalizeUrl($url))){
            return Html::a($text, $url, $options);
        }
        
        return '';
        
    }
    
    protected static function normalizeUrl($route)
    {
        $route = (array) $route;
        $route = $route[0];
        
        $route = Yii::getAlias((string) $route);
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return ltrim($route, '/');
        }

        // relative route
        if (Yii::$app->controller === null) {
            throw new InvalidParamException("Unable to resolve the relative route: $route. No active controller is available.");
        }

        if (strpos($route, '/') === false) {
            // empty or an action ID
            return $route === '' ? Yii::$app->controller->getRoute() : Yii::$app->controller->getUniqueId() . '/' . $route;
        } else {
            // relative to module
            return ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
        }
    }
    
    /**
     * crea un parametro estandar para el ID del BODY (html) en base a la url actual
     *
     */
    public static function getBodyId(){
        $currentURL = Url::current();
        $isPrettyUrl = Yii::$app->urlManager->enablePrettyUrl;
        $patterns = '';
        $replaces = '';
        
        if($isPrettyUrl){
            $patterns = array('/\?.*/' , '/\//');
            $replaces = array('' , '-');
        }
        else{
            $patterns = array('/\&.*/' , '/%2F/', '/.*r=/');
            $replaces = array('' , '-' , '');
        }
        $stringRep = preg_replace($patterns, $replaces , $currentURL);
        return $bodyId = ltrim($stringRep, '-');
    }
    
}
