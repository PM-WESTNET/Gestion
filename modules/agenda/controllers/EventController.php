<?php

namespace app\modules\agenda\controllers;

use Yii;
use app\modules\agenda\models\Event;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller 
{

    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Event::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Event();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->event_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->event_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @brief Devuelve un json con html para renderizar un evento de nota con nombre de usuario, fecha y contenido de la nota
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionBuildNote() {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $json = [];

        if ($post = Yii::$app->request->post()) {

            $type = $post['type'];
            $username = $post['username'];
            $body = $post['body'];
            $time = time();

            if(!empty($type) && !empty($username) && !empty($body)){
            
                $json['status'] = 'success';
                $json['html'] = $this->renderAjax('build_note', [
                    'type' => $type,
                    'username' => $username,
                    'body' => $body,
                    'time' => $time,
                ]);
                
            }else
                $json['status'] = 'error';

            return $json;
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
