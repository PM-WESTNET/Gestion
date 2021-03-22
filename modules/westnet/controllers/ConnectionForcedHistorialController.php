<?php

namespace app\modules\westnet\controllers;

use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\ConnectionForcedHistorial;
use app\modules\westnet\models\search\ConnectionForcedHistorialSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ConnectionForcedHistorialController implements the CRUD actions for ConnectionForcedHistorial model.
 */
class ConnectionForcedHistorialController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ConnectionForcedHistorial models.
     * @return mixed
     */
    public function actionIndex($connection_id)
    {
        $searchModel = new ConnectionForcedHistorialSearch();
        $searchModel->connection_id= $connection_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $connection= \app\modules\westnet\models\Connection::findOne(['connection_id' => $connection_id]);
        $contract= Contract::findOne(['contract_id' => $connection->contract_id]);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'contract'=> $contract,
        ]);
    }

    /**
     * Displays a single ConnectionForcedHistorial model.
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
     * Creates a new ConnectionForcedHistorial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ConnectionForcedHistorial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->connection_forced_historial_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ConnectionForcedHistorial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->connection_forced_historial_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ConnectionForcedHistorial model.
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
     * Finds the ConnectionForcedHistorial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ConnectionForcedHistorial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ConnectionForcedHistorial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
