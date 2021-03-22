<?php

namespace app\modules\automaticdebit\controllers;

use app\modules\automaticdebit\models\Bank;
use Yii;
use app\modules\automaticdebit\models\BankCompanyConfig;
use app\modules\automaticdebit\models\BankCompanyConfigSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BankCompanyConfigController implements the CRUD actions for BankCompanyConfig model.
 */
class BankCompanyConfigController extends Controller
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
     * Lists all BankCompanyConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankCompanyConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BankCompanyConfig model.
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
     * Creates a new BankCompanyConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($bank_id)
    {
        $model = new BankCompanyConfig();
        $bank = Bank::findOne($bank_id);

        if (empty($bank)) {
            throw new BadRequestHttpException('Bank Not Found');
        }
        $model->bank_id = $bank->bank_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('app','Company added successfull'));
            return $this->redirect(['bank/view', 'id' => $bank->bank_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'bank' => $bank
        ]);
    }

    /**
     * Updates an existing BankCompanyConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('app','Company updated successfull'));
            return $this->redirect(['bank/view', 'id' => $model->bank_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BankCompanyConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['bank/view', 'id' => $model->bank_id]);
    }

    /**
     * Finds the BankCompanyConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BankCompanyConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BankCompanyConfig::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
