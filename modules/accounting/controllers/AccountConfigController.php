<?php

namespace app\modules\accounting\controllers;

use app\components\web\Controller;
use app\modules\accounting\models\AccountConfigHasAccount;
use Yii;
use app\modules\accounting\models\AccountConfig;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AccountConfigController implements the CRUD actions for AccountConfig model.
 */
class AccountConfigController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'deleteAccount' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all AccountConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AccountConfig::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccountConfig model.
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
     * Creates a new AccountConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AccountConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->account_config_id]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getAccountConfigHasAccounts(),
            ]);

            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing AccountConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->account_config_id]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getAccountConfigHasAccounts(),
            ]);
            return $this->render('update', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Deletes an existing AccountConfig model.
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
     * Finds the AccountConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountConfig::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionAddAccount($id)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $acc = new AccountConfigHasAccount();
        $acc->load(Yii::$app->request->post());

        if($acc->validate()){

            $model = $this->findModel($id);

            $accObj = $model->addAccount([
                'account_config_id'=>$id,
                'account_id'=>$acc->account_id,
                'is_debit'=>$acc->is_debit,
                'attrib'=>$acc->attrib
            ]);

            return [
                'status' => 'success',
                'detail' => $accObj
            ];

        } else {
            return [
                'status' => 'error',
                'errors' => \yii\widgets\ActiveForm::validate($acc)
            ];

        }
    }

    /**
     * Deletes an existing AccountConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteAccount($account_config_id, $account_id)
    {
        $modelDelete = AccountConfigHasAccount::findOne([
            'account_config_id'=> $account_config_id,
            'account_id'=> $account_id]);
        if(!empty($modelDelete)) {
            $modelDelete->delete();
        }

        return $this->redirect(['update', 'id'=>
            $account_config_id
        ]);

    }
}
