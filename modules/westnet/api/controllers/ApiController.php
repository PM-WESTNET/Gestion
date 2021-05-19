<?php

namespace app\modules\westnet\api\controllers;

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
                'restUrl' => \yii\helpers\Url::to(['/isp/api/api/api'], true),
            ],
            //The resultUrl action.
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                //The scan directories, you should use real path there.
                'scanDir' => [
                    \Yii::getAlias('@app/modules/westnet/api/swagger'),
                    \Yii::getAlias('@app/modules/westnet/api/controllers'),
                    \Yii::getAlias('@app/modules/westnet/api/models'),
                    \Yii::getAlias('@app/modules/westnet/api/models'),
                ],
                //The security key
                'api_key' => 'isp',
            ],
        ];
    }  
    
}
