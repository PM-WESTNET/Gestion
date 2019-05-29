<?php

namespace app\modules\automaticdebit\controllers;

use app\components\web\Controller;
use app\modules\automaticdebit\models\BankCompanyConfig;
use app\modules\automaticdebit\models\DirectDebitExport;
use Yii;
use app\modules\automaticdebit\models\Bank;
use app\modules\automaticdebit\models\BankSearch;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BankController implements the CRUD actions for Bank model.
 */
class BankController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Bank models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bank model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $configs = new ActiveDataProvider(['query' => BankCompanyConfig::find()->andWhere(['bank_id' => $model->bank_id])]);

        return $this->render('view', [
            'model' => $model,
            'configs' => $configs
        ]);
    }

    /**
     * Creates a new Bank model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bank();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->bank_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Bank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->bank_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Bank model.
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
     * Finds the Bank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bank::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionExports($bank_id) {
        $bank = Bank::findOne($bank_id);

        if (empty($bank)) {
            throw new BadRequestHttpException('Bank not found');
        }

        $query = DirectDebitExport::find()
            ->andWhere(['bank_id' => $bank_id])
            ->orderBy(['create_timestamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $this->render('exports', ['dataProvider' => $dataProvider, 'bank' => $bank]);
    }

    public function actionCreateExport($bank_id) {

        $bank = Bank::findOne($bank_id);

        if (empty($bank)) {
            throw new BadRequestHttpException('Bank not found');
        }

        $export = new DirectDebitExport();
        $export->bank_id = $bank->bank_id;

        if ($export->load(Yii::$app->request->post())){

        }

        return $this->render('create-export', ['export' => $export]);
    }

}
