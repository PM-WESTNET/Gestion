<?php

namespace app\components\helpers;

use Yii;

/**
 * Includes function for db data manipulation
 *
 * @author marcelo
 */
class FlashHelper {

    /**
     * return nil
     * 
     * @param ActiveRecord $model
     */
    public static function flashErrors($model) {
        foreach ($model->getErrors() as $attribute => $messages) {
            foreach ($messages as $message) {
                Yii::$app->session->setFlash('error', "$attribute: $message");
            }
        }
    }

}
