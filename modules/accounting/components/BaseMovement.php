<?php

namespace app\modules\accounting\components;
use app\modules\accounting\models\AccountMovement;
use Yii;
use yii\web\Application;

/**
 * Interface MovementInterface
 * Debe ser implementada para poder crear movimientos contables desde los Behaviors de otras clases.
 *
 * @package app\modules\accounting\components
 */
abstract class BaseMovement
{

    public function addMessage($type, $message)
    {
        if(Yii::$app instanceof Application) {
            Yii::$app->session->addFlash($type, $message);
        } else {
            echo $message;
        }
    }

    /**
     * @param $action string insert o update
     * @param $modelInstance object Instancia del modelo
     * @param $accountConfig  object Instancia de AccountConfig
     * @return mixed
     */
    public abstract function move($action, $modelInstance, $accountConfig);
}