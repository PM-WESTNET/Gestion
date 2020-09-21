<?php

namespace app\modules\westnet\controllers;

use Yii;
use app\modules\westnet\models\NodeChangeProcess;
use app\modules\westnet\models\search\NodeChangeProcessSearch;
use yii\data\ActiveDataProvider;
use yii\debug\models\timeline\DataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NodeChangeProcessController implements the CRUD actions for NodeChangeProcess model.
 */
class NodeChangeProcessController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NodeChangeProcess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NodeChangeProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NodeChangeProcess model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getNodeChangeHistories()
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new NodeChangeProcess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NodeChangeProcess();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->upload() && $model->save()) {
                return $this->redirect(['view', 'id' => $model->node_change_process_id]);
            } else{
                Yii::$app->session->setFlash('error', 'Error de validación en el modelo.');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing NodeChangeProcess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->node_change_process_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NodeChangeProcess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NodeChangeProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NodeChangeProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NodeChangeProcess::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Procesa el archivo, creando los registros correspondientes, y actualizando los nodos en las conexiones correspondientes.
     */
    public function actionProcessFile($id)
    {
        $model = $this->findModel($id);

        $result = $model->processFile();
        if($result['status']){
            Yii::$app->session->addFlash('success', 'Archivo procesado');
            if(!empty($result['errors'])){
                Yii::$app->session->addFlash('error', 'Errores presentes al procesar archivo');
                foreach ($result['errors'] as $error){
                    Yii::$app->session->addFlash('error', $error);
                }
            }
        } else {
            Yii::$app->session->addFlash('error', 'El archivo indicado no puede procesarse');
            foreach ($result['errors'] as $error){
                Yii::$app->session->addFlash('error', $error);
            }
        }

        return $this->redirect(['view', 'id' => $id]);
    }
}
