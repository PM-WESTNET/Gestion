<?php

namespace app\modules\firstdata\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\firstdata\models\FirstdataImport;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\firstdata\models\search\FirstdataImportSearch;
use app\modules\firstdata\models\search\FirstdataImportPaymentSearch;
use app\modules\firstdata\models\FirstdataCompanyConfig;

/**
 * FirstdataImportController implements the CRUD actions for FirstdataImport model.
 */
class FirstdataImportController extends Controller
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
     * Lists all FirstdataImport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FirstdataImportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FirstdataImport model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        $importPaymentSearch = new FirstdataImportPaymentSearch();
        $importPaymentSearch->firstdata_import_id = $model->firstdata_import_id;

        $dataProvider = $importPaymentSearch->search(Yii::$app->request->get());

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'search' => $importPaymentSearch
        ]);
    }

    /**
     * Creates a new FirstdataImport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FirstdataImport();
        //var_dump($model->uploadFiles());die();
        
        if ($model->load(Yii::$app->request->post()) && $model->uploadFiles() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->firstdata_import_id]);
        }

        $accounts = ArrayHelper::map(MoneyBoxAccount::find()
        ->all(), 'money_box_account_id', 'account.name');
         $companies_config = ArrayHelper::map(FirstdataCompanyConfig::find()->all(), 'firstdata_company_config_id', 'company.name');

        return $this->render('create', [
            'model' => $model,
            'accounts' => $accounts,
            'companies_config' => $companies_config
        ]);
    }

    /**
     * Updates an existing FirstdataImport model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->firstdata_import_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FirstdataImport model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        $transaction = Yii::$app->db->beginTransaction();
        try {

            //TODO: eliminar los pagos primero asociados a first_data_import($id)

            $payment_ids = Yii::$app
                ->db
                ->createCommand('select fip.payment_id from firstdata_import_payment fip
                where fip.firstdata_import_id = :import_id')
                ->bindValue('import_id',$id)
                ->queryAll();
            
            // third we delete the payment pivot table
            Yii::$app
                ->db
                ->createCommand()
                ->delete('firstdata_import_payment', ['firstdata_import_id' => $id])
                ->execute();
            
            foreach ($payment_ids as $payment_id){
                // first we delete the payment items
                Yii::$app
                    ->db
                    ->createCommand('delete from payment_item
                    where payment_item.payment_id = :payment_id')
                    ->bindValue('payment_id',$payment_id['payment_id'])
                    ->execute();

                // then we delete the payments
                Yii::$app
                    ->db
                    ->createCommand('delete from payment
                    where payment.payment_id = :payment_id')
                    ->bindValue('payment_id',$payment_id['payment_id'])
                    ->execute();
            }

            

            // and last, we delete the firstdata import
            Yii::$app
                ->db
                ->createCommand()
                ->delete('firstdata_import', ['firstdata_import_id' => $id])
                ->execute();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            var_dump($e);die();
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the FirstdataImport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FirstdataImport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FirstdataImport::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionClosePayments($id) {
        $model = $this->findModel($id);
        $errors = 0;
        foreach($model->firstdataImportPayments as $importPayment) {
            if ($importPayment->payment) {
                if ($importPayment->payment->close()) {
                    $importPayment->updateAttributes(['status' => 'success']);
                }else {
                    $importPayment->updateAttributes(['status' => 'error']);
                    Yii::$app->session->addFlash('error', Yii::t('app','Can`t close payment: {payment}', ['payment' => $importPayment->payment->payment_id]));
                    $errors++;
                }
            }
        }

        if ($errors == 0) {
            Yii::$app->session->addFlash('success', 'Payments closed successfully');
        }else {
            Yii::$app->session->addFlash('warning', 'Any payments cant be closed');

        }

        $model->updateAttributes(['status' => 'success']);

        return $this->redirect(['view', 'id' => $model->firstdata_import_id]);

    }
}
