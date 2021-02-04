<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 04/05/18
 * Time: 16:36
 */

namespace app\modules\mobileapp\v1\components;


use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\AuthToken;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class Controller extends ActiveController
{
    protected $customer_token_validation= true;
    protected $exclude_actions= [];


    public function behaviors()
    {
        $behaviors= parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => ['*'],
                'Access-Control-Allow-Origin' => ['*', 'http://localhost:8080'],
                'Access-Control-Request-Method' => ['POST', 'PUT', 'OPTIONS', 'DELETE'],
                // Allow only POST and PUT methods
                'Access-Control-Request-Headers' => ['*'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Allow-Credentials' => null,
                // Allow OPTIONS caching
                'Access-Control-Max-Age' => 0,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Per-Page',
                    'X-Pagination-Total-Count',
                    'X-private-token',
                    'Auth-token',
                    'X-Pagination-Total-Count',
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count'],
            ],

        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {

        $parent = parent::beforeAction($action);

        if (\Yii::$app->request->isOptions) {
            return $parent;
        }

        $headers =\Yii::$app->request->headers;

        if(in_array($action->id, ['bill-pdf', 'pdf'])){
            return $parent;
        }

        //Verifico que venga y sea valido el token privado
        if ((!$headers->has('X-private-token')) || ($headers->get('X-private-token') !== Config::getValue('private_token'))){
            throw new ForbiddenHttpException('No Private Token');

        }

        // Si la validacion del cliente esta deshabilitada, puede continuar con la accion
        if ($this->customer_token_validation === false){
            return $parent;
        }

        // Si la validacion del cliente esta activa, verifico que action no este entre las excluidas
        if (array_search(\Yii::$app->controller->action->id, $this->exclude_actions) !== false){
            return $parent;
        }

        // Por ultimo verifico que venga y sea valido el token del cliente
        if (!$headers->has('Auth-token') || !AuthToken::validateToken($headers->get('Auth-token'))){
            throw new ForbiddenHttpException('No Auth Token');
        }

        return $parent;
    }

    protected function getUserApp(){
        $token= AuthToken::findOne(['token' => \Yii::$app->request->headers->get('Auth-token')]);

        if (empty($token)){
            throw new ForbiddenHttpException('Auth Token not found');
        }


        return $token->userApp;
    }

}