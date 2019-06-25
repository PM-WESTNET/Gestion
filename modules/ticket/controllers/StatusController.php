<?php

namespace app\modules\ticket\controllers;

use Yii;
use app\modules\ticket\models\Status;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\ticket\models\StatusHasAction;

/**
 * StatusController implements the CRUD actions for Status model.
 */
class StatusController extends Controller
{

    /**
     * Lists all Status models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Status::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Status model.
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
     * Creates a new Status model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Status();
        $status_has_action = new StatusHasAction();
        $post = Yii::$app->request->post();

        if ($model->load($post) && $status_has_action->load($post)) {
            if($model->save()){
                if($model->generate_action) {
                    $status_has_action->status_id = $model->status_id;
                    if($status_has_action->save()) {
                        return $this->redirect(['view', 'id' => $model->status_id]);
                    }
                }
                return $this->redirect(['view', 'id' => $model->status_id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'status_has_action' => $status_has_action
            ]);
        }
    }

    /**
     * Updates an existing Status model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $status_has_action = $model->generate_action ? $model->actionConfig : new StatusHasAction();
        $post = Yii::$app->request->post();

        if ($model->load($post) && $status_has_action->load($post) && $model->save()){
            if($model->generate_action) {
                $status_has_action->status_id = $model->status_id;
                if($status_has_action->save()) {
                    return $this->redirect(['view', 'id' => $model->status_id]);
                }
            }
            return $this->redirect(['view', 'id' => $model->status_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'status_has_action' => $status_has_action
            ]);
        }
    }

    /**
     * Deletes an existing Status model.
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
     * Finds the Status model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Status the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Status::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
