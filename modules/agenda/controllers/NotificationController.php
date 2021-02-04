<?php

namespace app\modules\agenda\controllers;

use Yii;
use app\modules\agenda\models\Notification;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\agenda\models\search\NotificationSearch;
/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends Controller
{

    /**
     * Lists all Notification models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotificationSearch;
        $searchModel->user_id = Yii::$app->user->getId();
        
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Notification model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Notification model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Notification();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->notification_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Notification model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->notification_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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

    /**
     * Finds the Notification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notification::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * @brief Cambia el estado de una notificación
     */
    public function actionChangeStatus(){
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $json = [];
        
        if($post = Yii::$app->request->post()){
                        
            $status = $post['status'];
            $id = $post['id'];
            
            $notification = $this->findModel($id);
            $notification->status = $status;
                        
            if($notification->save()){
                $json['status'] = "success";
                $json['notification'] = $notification;
                $json['message'] = "Estado cambiado"; 
            }
            else{
                $json['status'] = "error";
                $json['message'] = "Error";
            }
            
            return $json;
            
        }else{
            
            throw new NotFoundHttpException('The requested page does not exist.');
            
        }        
        
    }
    
    /**
     * @brief Cambia el estado de un grupod de notificaciones
     */
    public function actionBatchChangeStatus(){
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $json = [];
        
        if($post = Yii::$app->request->post()){
                        
            $status = $post['status'];
            $ids = $post['notificationsIds'];
            
            $responseStatus = true;
            $notifications = [];
            
            if(!empty($ids)){
                foreach($ids as $notificationId){                    
                    
                    $notification = $this->findModel($notificationId);
                    $notification->status = $status;
                    
                    if($notification->save())
                        $notifications[] = $notificationId;
                    else                        
                        $responseStatus = false;

                }
            }
                        
            if($responseStatus){                
                $json['status'] = "success";
                $json['notifications'] = $notifications;
                $json['message'] = "Estados cambiados"; 
            }else{
                $json['status'] = "error";
                $json['message'] = "Error";
            }
            
            return $json;
            
        }else{
            
            throw new NotFoundHttpException('The requested page does not exist.');
            
        }        
        
    }
}
