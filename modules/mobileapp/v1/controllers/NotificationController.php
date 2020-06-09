<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 07/05/18
 * Time: 10:28
 */

namespace app\modules\mobileapp\v1\controllers;

use app\modules\mobileapp\v1\components\Controller;
use app\modules\mobileapp\v1\models\MobilePushHasUserApp;
use app\modules\mobileapp\v1\models\UserApp;
use yii\web\Response;

class NotificationController extends Controller
{
    public $modelClass = 'app\modules\mobileapp\v1\models\UserApp';

    public function actions()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create'], $actions['update'], $actions['view']);

        return $actions;
    }

    public function verbs()
    {
        return [
            'notifications' => ['GET', 'OPTIONS'],
        ];
    }

    /**
     *
     * Devuelve la lista de ecopagos si el cliente posee una company que tenga habilitada los ecopagos, sino devuelve
     * un array vacio
     *
     * @return array
     */
    public function actionNotifications(){
//        $userApp = $this->getUserApp();
        $userApp = UserApp::findOne(18);

        if($userApp){
            \Yii::$app->response->setStatusCode(200);
            return [
                'notifications' => $userApp->notifications
            ];
        }

        \Yii::$app->response->setStatusCode(400);
        return ['notifications' => []];
    }

    /**
     * Marca una notificación como leída
     */
    public function actionSetAsRead($mobile_push_has_user_app_id){
//        $userApp = $this->getUserApp();
        $userApp = UserApp::findOne(18);

        if($userApp){
            \Yii::$app->response->setStatusCode(200);
            return  MobilePushHasUserApp::markAsRead($mobile_push_has_user_app_id);
        }

        \Yii::$app->response->setStatusCode(400);
        return false;
    }
}