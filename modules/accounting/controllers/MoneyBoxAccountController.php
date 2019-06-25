<?php

namespace app\modules\accounting\controllers;

use app\components\helpers\ExcelExporter;
use app\components\web\Controller;
use app\modules\accounting\models\MoneyBox;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\accounting\models\search\MoneyBoxAccountSearch;
use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\accounting\models\SmallBox;
use PHPExcel_Style_NumberFormat;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MoneyBoxAccountController implements the CRUD actions for MoneyBoxAccount model.
 */
class MoneyBoxAccountController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all MoneyBoxAccount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MoneyBoxAccount::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MoneyBoxAccount model.
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
     * Creates a new MoneyBoxAccount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MoneyBoxAccount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->money_box_account_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MoneyBoxAccount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->money_box_account_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MoneyBoxAccount model.
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
     * Finds the MoneyBoxAccount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MoneyBoxAccount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MoneyBoxAccount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Muestra todos los movimientos realizados en la cuenta.
     *
     * @param $id
     */
    public function actionMovements($id, $init= null)
    {
        $this->layout= '/fluid';
        $model = $this->findModel($id);
        
        $account = null;
        $fromMoneyBox = false;
        if (!empty($model->account)) {
            $account = $model->account;
        } else {
            if(!empty($model->moneyBox->account)) {
                $account = $model->moneyBox->account;
                $fromMoneyBox = true;
            }
        }

        if (!$account) {
            return $this->redirect(['index']);
        } else {
            $searchModel = new AccountMovementSearch();

            $params = Yii::$app->request->getQueryParams();
            if (!isset($params['AccountMovementSearch']['toDate'])) {
                $searchModel->initStatusDate = (new \DateTime())->modify('first day of this month')->format('Y-m-d');
            }
            $searchModel->account_id_from = $account->lft;
            $searchModel->account_id_to = $account->rgt;
            $searchModel->account_id = $account->account_id;

            $dataProvider = $searchModel->search($params, 1);
            
            return $this->render('movements', [
                'model'         => $model,
                'data'  => $dataProvider,
                'searchModel'   => $searchModel,
                'fromMoneyBox'  => $fromMoneyBox,
                'init' => $init
                
            ]);
        }
    }

    /**
     * Retorno el response necesario
     *
     * @param $id
     * @param null $init
     */
    private function dailyBoxMovements($id, $init = null, $export = false)
    {
        $model = $this->findModel($id);

        if($model->type !== 'daily'){
            throw new HttpException(500, Yii::t('app', 'This account is not a small box.'));
        }

        $account = $model->account;

        $searchModel = new AccountMovementSearch();
        $params = Yii::$app->request->getQueryParams();

        if(empty($params['AccountMovementSearch']['date'])){
            $params['AccountMovementSearch']['date'] = date('d-m-Y');
        }

        $searchModel->account_id_from = $account->lft;
        $searchModel->account_id_to = $account->rgt;
        $dataProvider = $searchModel->searchForDailyBox($params);
        
        /**
        $summaryModel = new AccountMovementSearch();
        $summaryModel->account_id_from = $account->lft;
        $summaryModel->account_id_to = $account->rgt;
        $summaryModel->date= date('d-m-Y', strtotime('-1 day'));
        $summaryModel->searchForDailyBox([]);
        **/
        
        $init_balance_day= $searchModel->statusAccount()['balance'];
        $init_debit_day=$searchModel->statusAccount()['debit'];
        $init_credit_day= $searchModel->statusAccount()['credit'];
        
        if(!$export) {
            return $this->render('daily-box-movements', [
                'model'         => $model,
                'data'  => $dataProvider,
                'searchModel'   => $searchModel,
                'init' => $init,
                'init_balance' => $init_balance_day,
                'init_credit_day' => $init_credit_day,
                'init_debit_day' => $init_debit_day,
                
            ]);
        } else {
            return [
                'model'         => $model,
                'data'          => $dataProvider,
                'searchModel'   => $searchModel,
                'init' => $init,
                'init_balance' => $init_balance_day,
                'init_credit_day' => $init_credit_day,
                'init_debit_day' => $init_debit_day,
            ];
        }
    }
    
    /**
     * Muestra todos los movimientos realizados en la cuenta.
     *
     * @param $id
     */
    public function actionDailyBoxMovements($id, $init= null)
    {
        return $this->dailyBoxMovements($id, $init);
    }

    /**
     * Muestra todos los movimientos realizados en la cuenta.
     *
     * @param $id
     */
    public function actionExport($id, $init= null)
    {
        $data =  $this->dailyBoxMovements($id, $init, true);

        $searchModel = $data['searchModel'];
        
        $balance = $searchModel->totalDebit - $searchModel->totalCredit;

        $excel = ExcelExporter::getInstance();
        $excel->create('caja-chica', [
            'A' => ['date',  Yii::t('app', 'Date'), PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY ],
            'B' => ['number', Yii::t('app', 'From / To'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'C' => ['description', Yii::t('app', 'Description'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
            'D' => ['debit', Yii::t('app', 'Debit'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],
            'E' => ['credit', Yii::t('app', 'Credit'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],
            'F' => ['balance', Yii::t('app', 'Balance'), PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00],
            'G' => ['status', Yii::t('app', 'Status'), PHPExcel_Style_NumberFormat::FORMAT_TEXT],
        ])->createHeader();

        //Encabezado:
        foreach ($data['data'] as $key => $movement){
            $excel->writeRow([
                'date'=> Yii::$app->formatter->asDate($movement['date'], 'dd-MM-yyyy'),
                'number'=> (($movement['debit'] == 0) ? $movement['from'] : Yii::t('accounting', 'To') . " ". $movement['from'] ),
                'description'=> $movement['description'],
                'debit'=> $movement['debit'],
                'credit'=> $movement['credit'],
                'balance'=> $movement['partial_balance'],
                'status'=> Yii::t('accounting', ucfirst($movement['status']))
            ]);
        }
        $excel->writeRow([
            '',
            '',
            '',
            Yii::$app->formatter->asCurrency($searchModel->totalDebit),
            Yii::$app->formatter->asCurrency($searchModel->totalCredit),
        ],0, false);

        $excel->setRow($excel->getRow()+2);

        $excel->writeRow([
            Yii::t('accounting', 'Init Balance') ,
            Yii::$app->formatter->asCurrency($data['init_balance']),
        ],0, false);

        $excel->setRow($excel->getRow()+1);

        $excel->writeRow([
            Yii::t('accounting', 'Balance of the day'),
        ],0, false);

        $excel->writeRow([
            Yii::t('accounting', 'Debit'), Yii::$app->formatter->asCurrency($searchModel->totalDebit),
            Yii::t('accounting', 'Credit'), Yii::$app->formatter->asCurrency($searchModel->totalCredit),
            Yii::t('app', 'Balance'), Yii::$app->formatter->asCurrency( $searchModel->totalDebit - $searchModel->totalCredit )
        ],0, false);

        $excel->setRow($excel->getRow()+1);
        $excel->writeRow([
            Yii::t('accounting', 'Total Account'),
        ],0, false);

        $excel->writeRow([
            Yii::t('accounting', 'Debit'),Yii::$app->formatter->asCurrency($data['init_debit_day'] + $searchModel->totalDebit),
            Yii::t('accounting', 'Credit'), Yii::$app->formatter->asCurrency($data['init_credit_day'] + $searchModel->totalCredit),
            Yii::t('app', 'Balance'), Yii::$app->formatter->asCurrency( $data['init_balance'] + $balance )
        ],0, false);

        $excel->download('caja-chica.xls');

    }
    
    public function actionCloseDailyBox($id, $date = null)
    {
        $model = $this->findModel($id);
        
        if($model->type !== 'daily'){
            throw new HttpException(500, Yii::t('app', 'This account is not a daily box.'));
        }
        
        if(!$date){
            $date = date('Y-m-d');
        }
        
        $model->closeDailyBox($date);
        
        Yii::$app->session->setFlash('success', Yii::t('accounting', 'The closing has been successful.'));
        
        $this->redirect(['daily-box-movements', 'id' => $id, 'AccountMovementSearch[date]' => Yii::$app->formatter->asDate($date, 'yyyy-MM-dd')]);
        
    }
    
     /**
     * Muestra todos los movimientos realizados en la cuenta.
     *
     * @param $id
     */
    public function actionSmallBoxMovements($id)
    {
        $smallbox = SmallBox::findOne(['small_box_id'=> $id]);
        
        
        $account = $smallbox->moneyBoxAccount->account;

        $searchModel = new AccountMovementSearch();
             
        
        $searchModel->account_id_from = $account->lft;
        $searchModel->account_id_to = $account->rgt;
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());
        
        $summaryModel = new AccountMovementSearch();
        $summaryModel->account_id_from = $account->lft;
        $summaryModel->account_id_to = $account->rgt;
        $summaryModel->search([]);
        
        return $this->render('small-box-movements', [
            'model'         => $smallbox,
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'summaryModel'  => $summaryModel
        ]);
    }
    
    public function actionOpenSmallBox($money_box_account_id){
        
        $smallBox= new SmallBox();
        $smallBox->money_box_account_id= $money_box_account_id;
        $smallBox->start_date= date('Y-m-d');
        $smallBox->initBalance();
        $smallBox->status= 'open';
        
        if ($smallBox->save(false)) {
            $this->redirect(['small-box-movements', 
                'id' => $money_box_account_id,]);

        }else{
            
        }
    }
    
    public function actionCloseSmallBox($id)
    {
        $smallBox = SmallBox::findOne(['small_box_id' => $id]);
        
        if ($smallBox->close()) {
            Yii::$app->session->setFlash('success', Yii::t('accounting', 'The closing has been successful.'));
        }else{
            Yii::$app->session->setFlash('error', Yii::t('accounting', 'Can`t do the closing'));
        }        
        
        $this->redirect(['small-box-movements', 'id' => $id]);
        
    }

    public function actionMoneyboxaccounts()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $money_box_id = $parents[0];
                $out = MoneyBoxAccount::find()->select(['money_box_account_id as id', 'number as name'])->where(['=', 'money_box_id', $money_box_id])->asArray()->all();
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }
}