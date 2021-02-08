<?php

namespace app\modules\ivr\v1\controllers;

use yii\web\Controller;

/**
 * Default controller for the `v1` module
 */
class ApiController extends Controller
{


    public function actions()
    {
        return [
            //The document preview addesss:http://api.yourhost.com/site/doc
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/ivr/v1/api/api'], true),
            ],
            //The resultUrl action.
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                //The scan directories, you should use real path there.
                'scanDir' => [
                    \Yii::getAlias('@app/modules/ivr/v1/swagger'),
                    \Yii::getAlias('@app/modules/ivr/v1/controllers'),
                    \Yii::getAlias('@app/modules/ivr/v1/models'),
                    \Yii::getAlias('@app/modules/ivr/v1/models'),
                ],
                //The security key
                'api_key' => 'balbalbal',
            ],
        ];
    }
}
