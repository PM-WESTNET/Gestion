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
        $errors = [];

        foreach ($model->getErrors() as $attribute => $messages) {
            foreach ($messages as $message) {
                if (!Yii::$app instanceof Yii\console\Application) {
                    Yii::$app->session->setFlash('error', "$attribute: $message");
                } else {
                    array_push($errors, "$attribute: $message");
                }
            }
        }

        if(Yii::$app instanceof Yii\console\Application) {
            Yii::$app->cache->set('_invoice_create_errors', $errors);
        }
    }

}
