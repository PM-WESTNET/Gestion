<?php

namespace app\modules\automaticdebit\controllers;

use app\components\web\Controller;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\automaticdebit\models\BankCompanyConfig;
use app\modules\automaticdebit\models\DebitDirectImport;
use app\modules\automaticdebit\models\DirectDebitExport;
use Yii;
use app\modules\automaticdebit\models\Bank;
use app\modules\automaticdebit\models\BankSearch;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\automaticdebit\models\DebitDirectImportHasPayment;
use app\modules\automaticdebit\models\DebitDirectFailedPayment;
use app\modules\checkout\models\Payment;

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

    /**
     * @param $bank_id
     * @return string
     * @throws BadRequestHttpException
     * Muestra un listado de las importaciones del banco
     */
    public function actionExports($bank_id)
    {
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

    /**
     * @param $export_id
     * @return string
     * Muestra los detalles de una exportaci??n
     */
    public function actionExportView($export_id)
    {
        $export = DirectDebitExport::findOne($export_id);

        return $this->render('export-view', ['export' => $export]);
    }

    /**
     * @param $bank_id
     * @return string
     * @throws BadRequestHttpException
     * Crea una nueva exportaci??n
     */
    public function actionCreateExport($bank_id)
    {

        $bank = Bank::findOne($bank_id);

        if (empty($bank)) {
            throw new BadRequestHttpException('Bank not found');
        }

        $export = new DirectDebitExport();
        $export->bank_id = $bank->bank_id;

        if ($export->load(Yii::$app->request->post()) && $export->generate()){
            $this->redirect(['export-view', 'export_id' => $export->direct_debit_export_id]);
        }

        return $this->render('create-export', ['export' => $export]);
    }

    /**
     * @param $export_id
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     * Descarga una exportaci??n
     */
    public function actionDownloadExport($export_id)
    {
        $export = DirectDebitExport::findOne($export_id);

        if (empty($export)) {
            throw new NotFoundHttpException('Export not found');
        }

        return Yii::$app->response->sendFile($export->file);
    }

    /**
     * @param $bank_id
     * @return string
     * Muestra un listado de las importaciones
     */
    public function  actionImports($bank_id)
    {
        $bank = Bank::findOne($bank_id);
        $imports = new ActiveDataProvider(['query' => DebitDirectImport::find()->andWhere(['bank_id' => $bank_id])->orderBy(['create_timestamp' => SORT_DESC])]);

        return $this->render('imports', ['dataProvider' => $imports, 'bank' => $bank]);

    }

    /**
     * @param $import_id
     * @return string
     * Muestra el detalle de una importaci??n
     */
    public function actionImportView($import_id)
    {
        $import = DebitDirectImport::findOne($import_id);

        return $this->render('import-view', ['import' => $import]);

    }

    /**
     * @param $import_id
     * @return string
     * Genere un archivo excel con los datos de los pagos fallidos
     */
    public function actionImportViewExport($import_id)
    {
        $import = DebitDirectImport::findOne($import_id);
        return $this->renderPartial('import-view-export', ['dataProviderFailedPayments' => $import->failedPayments]);

    }

    /**
     * @param $bank_id
     * @return string
     * @throws BadRequestHttpException
     * Crea una importaci??n
     */
    public function actionCreateImport($bank_id)
    {

        $bank = Bank::findOne($bank_id);

        if (empty($bank)) {
            throw new BadRequestHttpException('Bank not found');
        }

        $import = new DebitDirectImport();
        $import->bank_id = $bank->bank_id;

        if ($import->load(Yii::$app->request->post())) {
            $result_import = $import->import();
            if($result_import['status']){
                if($result_import['errors']) {
                    \Yii::$app->session->addFlash('error', $result_import['errors']);
                }
                $this->redirect(['import-view', 'import_id' => $import->debit_direct_import_id]);
            } else {
                \Yii::$app->session->setFlash('error', $result_import['errors']);
            }
        }

        $moneyBoxAccount = ArrayHelper::map(MoneyBoxAccount::find()->all(), 'money_box_account_id', 'number');

        return $this->render('create-import', ['import' => $import, 'moneyBoxAccount' => $moneyBoxAccount]);
    }

    /**
     * @param $import_id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * Cierra una importaci??n
     */
    public function actionCloseImport($import_id)
    {
        $import = DebitDirectImport::findOne($import_id);

        if (empty($import)) {
            throw new NotFoundHttpException('Import not found');
        }

        $close_payments = $import->closePayments();
        if(!$close_payments['status']){
            Yii::$app->session->addFlash('error', $close_payments['errors']);
            return $this->redirect(['import-view', 'import_id' => $import->debit_direct_import_id]);
        }

        Yii::$app->session->addFlash('success', Yii::t('app','Payments has been closed successfully'));
        return $this->redirect(['import-view', 'import_id' => $import->debit_direct_import_id]);
    }

    /**
     * deletes a existing import that created draft payments and are interfiering with the customers current money account
     * 
     * returns false if error
     */
    public function actionDeleteImport($import_id){
        // get import model
        $import = DebitDirectImport::findOne($import_id);

        //step 1: delete payments associated to the import. *including the pivot table registry

        // get the pivot table model based on import_id
        $debit_direct_import_has_payment_model = DebitDirectImportHasPayment::find()->where(['debit_direct_import_id' => $import_id])->all();

        // variable to check that all payments are actually draft. if one isnt, rollback and return an error.
        $error = false; 
        $transaction = Yii::$app->db->beginTransaction(); // transaction in case of error
        foreach($debit_direct_import_has_payment_model as $ddip){
            // get payment model
            $payment_model = Payment::findOne($ddip->payment_id);

            if(!empty($payment_model)){

                // if payment isnt draft checkout and notify error.
                if(!($payment_model->status == Payment::PAYMENT_DRAFT)){
                    $error = true;
                    break; // error break
                }

                // delete draft payment
                if($payment_model->delete()){
                    // delete pivot registers
                    if(!($ddip->delete())){
                        $error = true;
                        break;
                    }
                }
            }
            else{
                $ddip->delete();
            }
        }

        //step 2: delete failed payments from DebitDirectFailedPayment
        $failed_payments = DebitDirectFailedPayment::find()->where(['import_id'=>$import_id])->all();
        foreach($failed_payments as $payment){
            if( !($payment->delete()) ) $error = true;
        }

        //step 3: delete import
        if( !($import->delete()) ) $error = true;
        
        //step 4: commit or rollback
        $flash_type = 'success';
        $flash_msg = 'Draft payments and import registers deleted successfully';
        if($error) {
            $flash_type = 'error';
            $flash_msg = "The payment import could not be deleted. Some constraint was found or payment is already closed.";
            $transaction->rollback();
        }else{
            // commit changes
            $transaction->commit();
        }
            
        Yii::$app->session->addFlash($flash_type, $flash_msg);
        return $this->redirect(['imports', 'bank_id' => $import->bank_id]);
    }
}
