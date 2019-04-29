<?php

namespace app\modules\paycheck\controllers;

use app\modules\paycheck\models\Checkbook;
use app\modules\paycheck\models\search\PaycheckSearch;
use Yii;
use app\modules\paycheck\models\Paycheck;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaycheckController implements the CRUD actions for Paycheck model.
 */
class PaycheckController extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    
    /**
     * Lists all Paycheck models.
     * @return mixed
     */
    public function actionIndex($embed=false)
    {
        //Si se debe embeber la vista
        if($embed == true){
            $this->layout = '//embed';
        } else {
            $this->layout = '//fluid';
        }
            

        $searchModel = new PaycheckSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'embed'         => $embed

        ]);
    }

    /**
     * Displays a single Paycheck model.
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
     * Creates a new Paycheck model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($embed=false, $for_payment = true, $from_thrid_party = false)
    {
        $model = new Paycheck();

        //Si se debe embeber la vista
        if($embed == true){
            $this->layout = '//embed';
        }

        if ($model->checkbook_id) {
            $model->money_box_account_id = $model->checkbook->money_box_account_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($embed) {
                return $this->redirect([
                    'encartera',
                    'id' => $model->paycheck_id,
                    'embed' => $embed,
                    'for_payment' => $for_payment
                ]);

            } else {
                return $this->redirect([
                    'view',
                    'id' => $model->paycheck_id
                ]);

            }
        } else {
            if($embed !== true){
            return $this->render('create',[
                'model' => $model,
                'embed' => $embed,
                'for_payment' => $for_payment,
                'from_thrid_party' => $from_thrid_party

            ]);
            }else{
                return $this->renderAjax('create',[
                'model' => $model,
                'embed' => $embed,
                'for_payment' => $for_payment,
                'from_thrid_party' => $from_thrid_party
            ]);
            }
        }
    }

    /**
     * Updates an existing Paycheck model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->paycheck_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Paycheck model.
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
     * Finds the Paycheck model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Paycheck the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Paycheck::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCheckbooks()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $money_box_id = $parents[0];
                $out =  Checkbook::findActive($money_box_id)->select(['checkbook_id as id',

                        'CONCAT(\''.Yii::t('app', 'From').': \', start_number, \' - '.Yii::t('app', 'To' ).': \', end_number, \' - '.Yii::t('app', 'Last Used').': \',  last_used ) as name']
                )->asArray()->all();
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    /**
     * Lists all Paycheck models.
     * @return mixed
     */
    public function actionEncartera($embed=false, $for_payment=true)
    {
        //Si se debe embeber la vista
        if($embed == true){
            $this->layout = '//embed';
        }

        $searchModel = new PaycheckSearch();
        $dataProvider = $searchModel->searchEnCartera(Yii::$app->request->getQueryParams());
        
        //print_r($dataProvider,1); exit();

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'embed'         => $embed,
            'for_payment'    => $for_payment
        ]);
    }

    public function actionSelectPaycheck($id)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $paycheck = $this->findModel($id);

        return [
            'status'=>'success',
            'paycheck'=>$paycheck,
            'fullDescription' => $paycheck->getFullDescription()
        ];
    }

    public function actionChangeState($id)
    {
        if( Yii::$app->request->isAjax) {
            $model = $this->findModel($id);
            if ( Yii::$app->request->isGet) {
                return $this->renderAjax('_changeState',[
                    'model' => $model,
                ]);
            } else {
                $modelUpdate = new Paycheck();
                $modelUpdate->load(Yii::$app->request->post());
                Yii::$app->response->format = 'json';

                $model->dateStamp = $modelUpdate->dateStamp;

                $status = 'error';
                $transaction = Yii::$app->db->beginTransaction();
                if($model->can($modelUpdate->status) ){
                    $model->description = $modelUpdate->description;
                    $model->money_box_account_id = $modelUpdate->money_box_account_id;
                    if($model->changeState($modelUpdate->status)) {
                        $status = 'success';
                    }
                }

                if($status=='error') {
                    $transaction->rollback();
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('paycheck','The status was changed successfully.'));
                    $transaction->commit();
                }

                return [
                    'status' => $status,
                    'errors' => $model->getErrors()
                ];

            }
        }
    }
}