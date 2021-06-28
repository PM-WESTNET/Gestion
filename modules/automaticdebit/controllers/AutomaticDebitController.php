<?php

namespace app\modules\automaticdebit\controllers;

use app\modules\automaticdebit\models\Bank;
use Yii;
use app\modules\automaticdebit\models\AutomaticDebit;
use app\modules\automaticdebit\models\AutomaticDebitSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use webvimark\modules\UserManagement\models\User;

/**
 * AutomaticDebitController implements the CRUD actions for AutomaticDebit model.
 */
class AutomaticDebitController extends Controller
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
     * Lists all AutomaticDebit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AutomaticDebitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $userData = User::find()->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userData' => $userData,
        ]);
    }

    /**
     * Displays a single AutomaticDebit model.
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
     * Creates a new AutomaticDebit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AutomaticDebit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->automatic_debit_id]);
        }

        $banks = ArrayHelper::map(Bank::find()->andWhere(['status' => Bank::STATUS_ENABLED])->all(), 'bank_id', 'name');

        return $this->render('create', [
            'model' => $model,
            'banks' => $banks
        ]);
    }

    /**
     * Updates an existing AutomaticDebit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->automatic_debit_id]);
        }

        $banks = ArrayHelper::map(Bank::find()->andWhere(['status' => Bank::STATUS_ENABLED])->all(), 'bank_id', 'name');

        //print_r($banks);
        //die();

        return $this->render('update', [
            'model' => $model,
            'banks' => $banks
        ]);
    }

    /**
     * Deletes an existing AutomaticDebit model.
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
     * Finds the AutomaticDebit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AutomaticDebit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AutomaticDebit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
