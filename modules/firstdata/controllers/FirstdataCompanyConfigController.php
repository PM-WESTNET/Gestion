<?php

namespace app\modules\firstdata\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\firstdata\models\FirstdataCompanyConfig;
use app\modules\firstdata\models\search\FirstdataCompanyConfigSearch;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\Company;

/**
 * FirstdataCompanyConfigController implements the CRUD actions for FirstdataCompanyConfig model.
 */
class FirstdataCompanyConfigController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all FirstdataCompanyConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FirstdataCompanyConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FirstdataCompanyConfig model.
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
     * Creates a new FirstdataCompanyConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FirstdataCompanyConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->firstdata_company_config_id]);
        }

        $companies = ArrayHelper::map(Company::find()->andWhere(['status' => 'enabled'])->all(), 'company_id', 'name');

        return $this->render('create', [
            'model' => $model,
            'companies' => $companies
        ]);
    }

    /**
     * Updates an existing FirstdataCompanyConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->firstdata_company_config_id]);
        }

        $companies = ArrayHelper::map(Company::find()->andWhere(['status' => 'enabled'])->all(), 'company_id', 'name');

        return $this->render('update', [
            'model' => $model,
            'companies' => $companies
        ]);
    }

    /**
     * Deletes an existing FirstdataCompanyConfig model.
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
     * Finds the FirstdataCompanyConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FirstdataCompanyConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FirstdataCompanyConfig::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
