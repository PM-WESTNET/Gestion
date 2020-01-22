<?php

namespace app\modules\accounting\controllers;

use app\components\web\Controller;
use app\modules\accounting\models\Account;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\accounting\models\search\AccountMovementItemSearch;
use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\accounting\models\search\AccountSearch;
use Yii;
use app\modules\accounting\models\AccountMovement;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\accounting\models\MoneyBoxAccount;
use yii\web\Response;

/**
 * AccountMovementController implements the CRUD actions for AccountMovement model.
 */
class AccountMovementController extends Controller
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
     * Lists all AccountMovement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountMovementSearch();
        $dataProvider = $searchModel->searchForMovements(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single AccountMovement model.
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
     * Creates a new AccountMovement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=null)
    {
        $model = new AccountMovement();
        if($id){
            $model = $this->findModel($id);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['account-movement/update', 'id' => $model->account_movement_id]);
        } else {

            return $this->render('create', [
                'model' => $model,
                'itemsDataProvider' => null,
                
            ]);
        }
    }

    /**
     * Updates an existing AccountMovement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if($model->dailyMoneyBoxAccount){
            return $this->redirect(['daily-box-update', 'id' => $id]);
        }
        
        //No se puede:
        if($model->status !== 'draft' && $model->status !== 'broken' ){
            throw new \yii\web\HttpException(405, Yii::t('accounting', 'This movement is closed and can not be updated.'));
        }

        if ($model->load(Yii::$app->request->post()) ) {
            if($model->validateMovement() && $model->save()) {
                return $this->redirect(['view', 'id' => $model->account_movement_id]);
            } else {
                Yii::$app->session->addFlash('error', Yii::t('accounting', 'The movement is invalid.'));
            }

        }

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getAccountMovementItems()
        ]);

        return $this->render('update', [
            'model' => $model,
            'itemsDataProvider' => $itemsDataProvider,

        ]);
    }
    
    /**
     * Creates a new AccountMovement model for a small box.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionDailyBoxCreate($box_id, $date = null)
    {
        $model = new AccountMovement();
        
        /**
         * Es necesario un mecanismo para establecer sin lugar a duda que este
         * movimiento se trata de un movimiento de caja chica. Hasta el momento, con
         * los datos presentes solo se podia establecer la cuenta Account asociada
         * al item de movimiento; para el caso de un movimiento de caja chica,
         * no daba certeza de tratarse de un movimiento de caja chica real o un 
         * movimiento normal con multiples items donde alguno de ellos era de la
         * cuenta (Account) caja chica.
         */
        $model->daily_money_box_account_id = $box_id;
        $box = $model->dailyMoneyBoxAccount;
        
        //Fecha y hora no se pueden modificar
        $model->date = $date;
        if($date == null){
            $model->date = date('d-m-Y');
        } else {
            if( (new \DateTime($date))->format('Ymd') != (new \DateTime('now'))->format('Ymd')) {
                throw new \yii\web\HttpException(405, Yii::t('accounting', 'You can not add a move to a date other than today.'));
            }
        }
        $model->time = date('H:i');


        if($box->isDailyBoxClosed($model->date)){
            throw new \yii\web\HttpException(405, Yii::t('accounting', 'This small box is closed.'));
        }
        
        $item = new AccountMovementItem;
        $item->account_id = $box->account_id;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $transaction = $model->db->beginTransaction();
            try{
                if($model->save() && $item->load(Yii::$app->request->post())){
                    
                    $item->link('accountMovement', $model);
                    $transaction->commit();
                    
                    return $this->redirect(['view', 'id' => $model->account_movement_id]);

                }
                
            } catch(\Exception $ex) {
                $transaction->rollback();
                throw $ex;
            }
            
        } 

        return $this->render('daily_box_create', [
            'model' => $model,
            'item' => $item,
            'box' => $box
        ]);
    }
    
    public function actionSmallBoxCreate($box_id, $date = null)
    {
        $model = new AccountMovement();
        
        /**
         * Es necesario un mecanismo para establecer sin lugar a duda que este
         * movimiento se trata de un movimiento de caja chica. Hasta el momento, con
         * los datos presentes solo se podia establecer la cuenta Account asociada
         * al item de movimiento; para el caso de un movimiento de caja chica,
         * no daba certeza de tratarse de un movimiento de caja chica real o un 
         * movimiento normal con multiples items donde alguno de ellos era de la
         * cuenta (Account) caja chica.
         */
        $model->small_money_box_account_id = $box_id;
        $box = MoneyBoxAccount::findOne(['money_box_account_id' => $box_id]);
        
        
        //Fecha y hora no se pueden modificar
        $model->date = $date;
        if($date == null){
            $model->date = date('d-m-Y');
        }
        $model->time = date('H:i');
        
        
        
        $item = new AccountMovementItem;
        $item->account_id = $box->account_id;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $transaction = $model->db->beginTransaction();
            try{
                if($model->save() && $item->load(Yii::$app->request->post())){
                    
                    $item->link('accountMovement', $model);
                    $smallbox= \app\modules\accounting\models\SmallBox::findOne(['money_box_account_id' => $box->money_box_account_id]);
                    $smallbox->balance = (float)$smallbox->balance - (float)$item->credit + (float)$item->debit;
                    $smallbox->updateAttributes(['balance']);
                    $transaction->commit();
                    
                    return $this->redirect(['view', 'id' => $model->account_movement_id]);

                }
                
            } catch(\Exception $ex) {
                $transaction->rollback();
                throw $ex;
            }
            
        } 

        return $this->render('small_box_create', [
            'model' => $model,
            'item' => $item,
            'box' => $box
        ]);
    }

    /**
     * Updates an existing AccountMovement model from a small box.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDailyBoxUpdate($id)
    {
        $model = $this->findModel($id);
        
        if(!$model->dailyMoneyBoxAccount){
            return $this->redirect(['update', 'id' => $id]);
        }
        
        //No se puede:
        if($model->status !== 'draft'){
            throw new \yii\web\HttpException(405, Yii::t('accounting', 'This movement is closed and can not be updated.'));
        }
        
        $box = $model->dailyMoneyBoxAccount;
        $item = $model->accountMovementItems[0];
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $transaction = $model->db->beginTransaction();
            try{
                if($model->save() && $item->load(Yii::$app->request->post())){
                    
                    if($item->save()){
                        $transaction->commit();
                    }
                    
                    return $this->redirect(['view', 'id' => $model->account_movement_id]);

                }
                
            } catch(\Exception $ex) {
                $transaction->rollback();
                throw $ex;
            }
            
        } 

        return $this->render('daily_box_update', [
            'model' => $model,
            'item' => $item,
            'box' => $box
        ]);
    }
    
    public function actionSmallBoxUpdate($id)
    {
        $model = $this->findModel($id);
        
        if(!$model->smallMoneyBoxAccount){
            return $this->redirect(['update', 'id' => $id]);
        }
        
        //No se puede:
        if($model->status !== 'draft'){
            throw new \yii\web\HttpException(405, Yii::t('accounting', 'This movement is closed and can not be updated.'));
        }
        
        $box = $model->smallMoneyBoxAccount;
        $item = $model->accountMovementItems[0];
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
            $transaction = $model->db->beginTransaction();
            try{
                if($model->save() && $item->load(Yii::$app->request->post())){
                    
                    if($item->save()){
                        $transaction->commit();
                    }
                    
                    return $this->redirect(['view', 'id' => $model->account_movement_id]);

                }
                
            } catch(\Exception $ex) {
                $transaction->rollback();
                throw $ex;
            }
            
        } 

        return $this->render('small_box_update', [
            'model' => $model,
            'item' => $item,
            'box' => $box
        ]);
    }

    /**
     * Deletes an existing AccountMovement model.
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
     * Finds the AccountMovement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountMovement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountMovement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Lists all AccountMovement models.
     * @return mixed
     */
    public function actionResume()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('resume', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }



    /**
     * Agrega un item movimiento
     * @param $id
     */
    public function actionAddItem($account_movement_id=null, $account_movement_item_id=null)
    {
        $model = new AccountMovement();
        if($account_movement_id){
            $model = $this->findModel($account_movement_id);
        }
        
        $item = new AccountMovementItem();
        $account_movement_item_id = ($account_movement_item_id ?  $account_movement_item_id :
            (Yii::$app->request->post('AccountMovementItem')['account_movement_item_id'] ?
                Yii::$app->request->post('AccountMovementItem')['account_movement_item_id'] : null ) );
        
        if($account_movement_item_id) {
            $item = AccountMovementItem::findOne($account_movement_item_id);
        }
        
        if ($item->load(Yii::$app->request->post()) && $item->save()) {

            $item = new AccountMovementItem();
        }

        return $this->renderAjax('_form-items', [
            'model' => $model,
            'item'  => $item
        ]);
    }

    /**
     * Borra un item movimiento
     *
     * @param $id
     * @param $account_movement_item_id
     */
    public function actionDeleteItem($account_movement_id, $account_movement_item_id)
    {
        $item = AccountMovementItem::findOne( $account_movement_item_id );
        if($item) {
            $item->delete();
        }
        return $this->actionListItems($account_movement_id);
    }

    /**
     * Se listan los items del movimiento.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionListItems($account_movement_id)
    {
        $model = $this->findModel($account_movement_id);

        $items = new ActiveDataProvider([
            'query' => $model->getAccountMovementItems()
        ]);

        return $this->renderAjax('_form-list-items', [
            'model' => $model,
            'items' => $items
        ]);
    }

    /**
     * Se listan los items del movimiento.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionClose($id, $from = 'view', $from_date = '', $to_date = '', $money_box_account_id = null)
    {        
        $model = $this->findModel($id);

        $model->close();

        if($from == 'view'){
            return $this->actionView($id);
        } else {
            if ($from === 'movements' && !empty($money_box_account_id)) {// Si llamo la accion desde la pantalla de movimientos, vuelvo a la misma pantalla
                $this->redirect(\yii\helpers\Url::to(['/accounting/money-box-account/movements', 'id' => $money_box_account_id, 'init' => $_COOKIE['current']]).'&AccountMovementSearch%5BfromDate%5D='.$from_date.'&AccountMovementSearch%5BtoDate%5D='.$to_date);
            }else{
                return $this->actionIndex();
            }
        }
    }


    /**
     * Chequea un movimiento contable.
     *
     * @return array
     */
    public function actionCheck()
    {
        $account_movement_id = Yii::$app->request->post('account_movement_id', null);
        $checked = Yii::$app->request->post('checked', false);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $json = [
            'status' => 'error',
        ];

        if($account_movement_id) {
            $account_movement = AccountMovement::findOne(['account_movement_id'=> $account_movement_id]);
            $account_movement->check = $checked;
            try {
                $account_movement->update(false);
                $json['status'] = 'success';
            } catch (\Exception $ex) {
                $json['message'] = $ex->getMessage();
            }
        }

        return $json;
    }

    public function actionMayorBook($account_id) {

        $account = Account::findOne($account_id);

        if (empty($account)) {
            throw new BadRequestHttpException('Account not found');
        }

        $search = new AccountMovementSearch();
        $search->account_id = $account->account_id;

        $dataProvider = new ActiveDataProvider(['query' => $search->searchMayorBook(Yii::$app->request->getQueryParams()), 'pagination' => false]);


        return $this->render('mayor_book', ['dataProvider' => $dataProvider, 'account' => $account, 'search' => $search]);

    }


}