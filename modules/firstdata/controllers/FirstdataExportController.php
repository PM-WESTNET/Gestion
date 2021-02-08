<?php

namespace app\modules\firstdata\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\firstdata\models\FirstdataExport;
use app\modules\firstdata\models\FirstdataCompanyConfig;
use app\modules\firstdata\models\search\FirstdataExportSearch;

/**
 * FirstdataExportController implements the CRUD actions for FirstdataExport model.
 */
class FirstdataExportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all FirstdataExport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FirstdataExportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FirstdataExport model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FirstdataExport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FirstdataExport();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->firstdata_export_id]);
        }

        $companies_config = ArrayHelper::map(FirstdataCompanyConfig::find()->all(), 'firstdata_company_config_id', 'company.name');

        return $this->render('create', [
            'model' => $model,
            'companies_config' => $companies_config
        ]);
    }

    /**
     * Updates an existing FirstdataExport model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->firstdata_export_id]);
        }

        $companies_config = ArrayHelper::map(FirstdataCompanyConfig::find()->all(), 'firstdata_company_config_id', 'company.name');

        return $this->render('update', [
            'model' => $model,
            'companies_config' => $companies_config
        ]);
    }

    /**
     * Deletes an existing FirstdataExport model.
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
     * Finds the FirstdataExport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FirstdataExport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FirstdataExport::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Genera el archivo para firstdata
     */
    public function actionCreateFile($id) 
    {
        $model = $this->findModel($id);

        if (!$model->export()) {
            Yii::$app->session->addFlash('error', Yii::t('app', 'Cant generate Firstdata file'));
        }

        return $this->redirect(['view', 'id' => $model->firstdata_export_id]);
    }

    public function actionDownload($id) 
    {
        $model = $this->findModel($id);

        return Yii::$app->response->sendFile($model->file_url);
    }
}
