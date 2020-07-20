<?php

namespace app\modules\westnet\notifications\controllers;

use app\modules\sale\models\Company;
use app\modules\westnet\notifications\models\IntegratechReceivedSms;
use app\modules\westnet\notifications\models\Transport;
use app\modules\mailing\models\EmailTransport;
use app\modules\mailing\models\search\EmailTransportSearch;
use Yii;
use app\modules\westnet\notifications\models\Notification;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\notifications\models\search\NotificationSearch;
use app\modules\westnet\notifications\NotificationsModule;
use yii\web\Response;

/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Notification models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new NotificationSearch();
        $searchModel->enabled_transports_only = true;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    public function actionIndexProgrammed(){
        $searchModel = new NotificationSearch();
        $searchModel->enabled_transports_only = true;
        $searchModel->programmed = true;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index_programmed', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single Notification model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        return $this->create();
    }
    
    /**
     * Creates a new Notification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function create() {
        $model = new Notification();
        $model->scenario = 'create';

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return $this->redirect(['wizard', 'id' => $model->notification_id, 'step' => 1]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {

            return $this->render('create', [
                    'model' => $model,
            ]);
        }
    }
    
    public function update($id, $wizard = true)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                if($wizard){
                    return $this->redirect(['destinatary/create', 'notification_id' => $model->notification_id]);
                }else{
                    return $this->redirect(['view', 'id' => $model->notification_id]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Renderiza una vista para actualizar el estado de la notificacion.
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update-status';
        
        //Si es ajax, la rta es en json
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
        
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $transport = $model->transport;
            /**
             * Si le transport es Email, la marcamos como pendiente solamente
             * Luego el comando en segundo plano se encargara de realizar el envio
             */
            if ($transport->name === 'Email'|| $transport->name === 'Mobile Push') {
                $model->updateAttributes(['status' => 'pending']);
                Yii::$app->session->setFlash('success', NotificationsModule::t('app', 'The notifications has been sent.'));
                return $this->redirect(['view', 'id' => $id]);
            }

            if ($model->save()) {
                
                if(Yii::$app->request->isAjax){
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'status' => 'success',
                        'model' => $model
                    ];
                }
                
                if($model->status == 'enabled'){
                    Yii::$app->session->setFlash('success', NotificationsModule::t('app', 'The notification has been activated.'));
                }else{
                    Yii::$app->session->setFlash('success', NotificationsModule::t('app', 'The notification has been deactivated.'));
                }
                    
                return $this->redirect(['view', 'id' => $model->notification_id]);
            } else {
                
                if(Yii::$app->request->isAjax){
                    return [
                        'status' => 'error',
                        'errors' => $model->getErrors(),
                        'model' => $model
                    ];
                }
                
                return $this->render('update-status', [
                    'model' => $model,
                ]);
            }
        } else {
            
            if(Yii::$app->request->isAjax){
                return [
                    'status' => 'error',
                    'errors' => ['Bad request.'],
                    
                ];
            }
            
            return $this->render('update-status', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Organiza los pasos para crear una notificacion y ponerla en estado
     * habilitada para ser enviada.
     * @param int $id
     * @param int $step
     * @throws NotFoundHttpException
     */
    public function actionWizard($id = null, $step = 0) {
        
        /**
         * Primer paso: seleccion de transport y nombre.
         * En funcion del transport seleccionado, es el editor de contenido
         * que se mostrara (ej, sms solo texto y contador de caracteres y mensajes).
         */
        if($step == 0 || $id === null) {
            return $this->create();
        }elseif($id){
            
            /**
             * Pasos siguientes:
             */
            switch ($step){
                case 1:
                    return $this->update($id);
                    break;
            
                case 2:
                    return $this->redirect(['destinatary/create', 'notification_id' => $id]);
                    break;
                
                case 3:
                    $notification = $this->findModel($id);
                    if($notification->scheduler && $notification->status != 'enabled'){
                        return $this->redirect(['update-status', 'id' => $id]);
                    }else{
                        return $this->redirect(['view', 'id' => $id]);
                    }
                    break;
            }
        }
            
        throw new NotFoundHttpException('The requested page does not exist.');
        
    }
    
    /**
     * Updates an existing Notification model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        return $this->update($id, false);
    }

    /**
     * Sets the destinataries for a specific notification
     * @param type $id
     * @return mixed
     */
    public function actionDestinataries($id) {
        $model = $this->findModel($id);

        return $this->render('destinataries', [
            'model' => $model,
        ]);
    }

    /**
     * Returns calculated times on ajax calls
     * @param type $id
     */
    public function actionGetPeriodTimes() {
        
        $request = \Yii::$app->request;

        //If the request is not an ajax request, return an exception
        if (!$request->isAjax)
            throw new NotFoundHttpException('The requested page does not exist.');
        
        $notification = new Notification();
        $notification->times_per_day = $request->get('period');
        $notification->from_time = $request->get('timeFrom');
        $notification->to_time = $request->get('timeTo');
        $schedule = $notification->calcDailyPeriod();

        $json = [];
        $json['status'] = 'success';
        $json['html'] = $this->renderAjax('schedule', [
            'schedule' => $schedule,
        ]);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $json;
    }
    
    /**
     * Exporta los contactos con la info requerida por el tipo de transport
     * @param type $id
     */
    public function actionExport($id)
    {
        $model = $this->findModel($id);
        
        $model->transport->export($model);
        
    }
    
    /**
     * Envio manual de notificacion
     * @param type $notification_id
     */
    public function actionSend($id, $force_send = false)
    {
        $notification = $this->findModel($id);
        $transport = $notification->transport;

        try {
            $status = $transport->send($notification, $force_send);
            if($status['status'] == 'success'){
                if(array_key_exists('message', $status)){
                    Yii::$app->session->setFlash('info', $status['message']);
                } else {
                    Yii::$app->session->setFlash('success', NotificationsModule::t('app', 'The notifications has been sent.'));
                }
            } else {
                if(array_key_exists('error', $status)){
                    Yii::$app->session->setFlash('error', $status['error']);
                } else {
                    Yii::$app->session->setFlash('error', NotificationsModule::t('app', 'The notifications hasnt been sent.'));
                }
            }
        } catch(\Exception $ex) {
            throw $ex;
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
            
    }

    /**
     * Deletes an existing Notification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) 
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionLoadSchedulerForm($id, $class)
    {
        $model = $this->findModel($id);
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return [
            'status' => 'success',
            'model' => $model,
            'form' => $this->renderPartial('_scheduler-form', [
                'model' => $model,
                'scheduler' => \app\modules\westnet\notifications\components\scheduler\Scheduler::getSchedulerObject($class)
            ])
        ];
    }

    /**
     * Finds the Notification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Notification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Retorno todos los email transport asociados a la empresa.
     *
     * @return array
     */
    public function actionFindEmailTransports()
    {
        Yii::$app->response->format = 'json';

        $return = [
            'status' => 'ko'
        ];

        $transport_id = Yii::$app->request->post('transport_id');
        $company_id = Yii::$app->request->post('company_id');

        if($transport_id && $company_id) {
            $transport = Transport::findOne(['transport_id'=>$transport_id]);
            if($transport->slug == 'email' || $transport->slug == 'browser') {
                $data = [];
                $et = EmailTransport::findAll([
                    'relation_class'    => Company::class,
                    'relation_id'       => $company_id
                ]);
                if(count($et)) {
                    /** @var EmailTransport $item */
                    foreach ($et as $item) {
                        $data[] = $item;
                    }
                }
                $return = [
                    'status' => 'ok',
                    'data' => $data,
                    'transport' => $transport->slug
                ];
            }
        }
        return $return;
    }

    public function actionAbortSend($notification_id){
        $notification = Notification::findOne($notification_id);
        if($notification->transport->abortMessages($notification_id)){
            $notification->updateAttributes(['status' => Notification::STATUS_CANCELLED]);
        }

         return $this->redirect(['view', 'id' => $notification_id]);
    }

    /**
     * Recibe sms desde integratech que han sido respuesta de mensajes enviados a traves de notificaciones
     */
    public function actionSms(){

        $integratech_received_sms = new IntegratechReceivedSms();
        $integratech_received_sms->destaddr = Yii::$app->request->post('DESTADDR') ? Yii::$app->request->post('DESTADDR') : '';
        $integratech_received_sms->message = Yii::$app->request->post('MESSAGE') ? Yii::$app->request->post() :'';
        $integratech_received_sms->charcode = Yii::$app->request->post('CHARCODE') ? Yii::$app->request->post('CHARCODE') : '';
        $integratech_received_sms->sourceaddr = Yii::$app->request->post('SOURCEADDR') ? Yii::$app->request->post('SOURCEADDR') : '';
        $integratech_received_sms->save();
    }

    public function actionNotificationProccessStatus($id){
        if (Yii::$app->request->isAjax) {
            $model= $this->findModel($id);

            if ($model->transport->name === 'Email') {

                $total = Yii::$app->cache->get('total_'.$id);
                $status = $model->status;
                $ok = (int)Yii::$app->cache->get('success_'.$id);
                $error = (int)Yii::$app->cache->get('error_'.$id);
                $message = Yii::$app->cache->get('error_message_'.$id);
                
                Yii::$app->response->format= Response::FORMAT_JSON;
                
                return [
                    'status' => $status,
                    'total' => $total,
                    'success' => $ok,
                    'error' => $error,
                    'message' => $message
                ];
            }elseif($model->transport->name === 'Mobile Push')  {
                $total = Yii::$app->cache->get('notification_'.$id.'_total');
                $status = $model->status;
                $ok = (int)Yii::$app->cache->get('notification_'.$id.'_sended');
                $ok_with_errors= (int)Yii::$app->cache->get('notification_'.$id.'_with_errors');
                $error = (int)Yii::$app->cache->get('notification_'.$id.'_not_sended');
                $message = Yii::$app->cache->get('error_message_'.$id);
                
                Yii::$app->response->format= Response::FORMAT_JSON;
                
                if ($model->status === 'sent') {
                    Yii::$app->cache->set('notification_'.$id.'_total', 0);
                    Yii::$app->cache->set('notification_'.$id.'_sended', 0);
                    Yii::$app->cache->set('notification_'.$id.'_with_errors', 0);
                    Yii::$app->cache->set('notification_'.$id.'_not_sended', 0 );
                    Yii::$app->cache->set('error_message_'.$id, 0);
                }

                return [
                    'status' => $status,
                    'total' => $total,
                    'success' => $ok,
                    'success_errors' => $ok_with_errors,
                    'notSended' => $error,
                    'message' => $message
                ];
            }
        } 
    }
}
