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
        $userApp = $this->getUserApp();
        $from_date = (new \DateTime('now'))->modify('-6 months')->getTimestamp();

        if($userApp){
            \Yii::$app->response->setStatusCode(200);
            return [
                'notifications' => $userApp->getNotifications()->where(['>=','created_at', $from_date])->all()
            ];
        }

        \Yii::$app->response->setStatusCode(400);
        return ['notifications' => []];
    }


    /**
     * Devuelve una mobile push para el usuario logueado
     */
    public function actionView() {
        $userApp = $this->getUserApp();

        if ($userApp) {
            $mobile_push_id = \Yii::$app->request->post('mobile_push_id');

            if ($mobile_push_id) {
                $mphua = MobilePushHasUserApp::findOne(['mobile_push_id' => $mobile_push_id, 'user_app_id' => $userApp->user_app_id]);

                if ($mphua) {
                    return $mphua;
                }
            }
        }

        \Yii::$app->response->setStatusCode(400);
        return ['notification' => []];
    }

    /**
     * Marca una notificación como leída
     */
    public function actionSetAsRead(){
        $userApp = $this->getUserApp();
        $mphua = \Yii::$app->request->post('mphua_id');

        if (empty($mphua)) {
            \Yii::$app->response->setStatusCode(400);
            return [
                'msg' => 'No encuentro parámetro'
            ];
        }

        if($userApp){
            \Yii::$app->response->setStatusCode(200);
            $result = MobilePushHasUserApp::markAsRead($mphua);

            if ($result) {
                return [
                    'msg' => 'Marcado con éxito'
                ];
            }
        }

        \Yii::$app->response->setStatusCode(400);
        return [
            'msg' => 'No encuentro UserApp. Raro!!!'
        ];
    }

    /*
        Devuelve la cantidad de notificaciones sin leer que posee el userApp    
    */ 
    public function actionGetNotificationsCount() 
    {
        $userApp= $this->getUserApp();

        if (empty($userApp)) {
            return [
                'count' => 0,
            ];
        }

        $count = $userApp->getNotifications()->andWhere(['notification_read' => 0])->count();

        return [
            'count' => $count
        ];
    }
}