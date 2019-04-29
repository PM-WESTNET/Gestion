<?php

namespace app\modules\agenda\controllers;

use Yii;
use app\modules\agenda\models\Task;
use app\modules\agenda\models\search\TaskSearch;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller 
{

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new TaskSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($async = false) {
        $model = new Task();

        if ($async)
            $this->layout = 'agenda_empty';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionQuickCreate($date = null) {
        $model = new Task();

        if (!empty($date))
            $model->date = $date;

        $this->layout = 'agenda_empty';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render('quick-create', [
                        'model' => $model,
                        'message' => 'Tarea creada con éxito.',
            ]);
        } else {
            return $this->render('quick-create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionQuickUpdate($id) {
        $this->layout = 'agenda_empty';
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render('quick-update', [
                        'model' => $model,
                        'message' => \app\modules\agenda\AgendaModule::t('app', 'Changes successfully saved'),
            ]);
        } else {
            return $this->render('quick-update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Pospone una tarea determinada
     * @param type $id
     * @return mixed
     */
    public function actionPostponeTask($id) {
        $this->layout = 'agenda_empty';
        $model = $this->findModel($id);

        if (!empty($model)) {

            //Posponemos la tarea para este usuario
            \app\modules\agenda\components\AgendaAPI::postponeTask($model, $_POST['Task']['date'], [
                \Yii::$app->user->id
            ]);

            return $this->render('quick-update', [
                        'model' => $model,
                        'message' => \app\modules\agenda\AgendaModule::t('app', 'Task successfully postponed'),
            ]);
        } else {

            return $this->render('quick-update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
