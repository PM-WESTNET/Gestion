<?php

namespace app\modules\sale\controllers;

use app\modules\sale\models\Product;
use Yii;
use app\modules\sale\models\FundingPlan;
use app\modules\sale\models\search\FundingPlanSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FundingPlanController implements the CRUD actions for FundingPlan model.
 */
class FundingPlanController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }
    /**
     * Lists all FundingPlan models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $product = Product::findOne($id);

        $dataProvider = new ActiveDataProvider([
            'query' => $product->getFundingPlan(),
        ]);

        return $this->render('index', [
            'product' => $product,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FundingPlan model.
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
     * Creates a new FundingPlan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $product = Product::findOne($id);
        $model = new FundingPlan();
        $model->product_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->funding_plan_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'product' => $product
            ]);
        }
    }

    /**
     * Updates an existing FundingPlan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->funding_plan_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing FundingPlan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $product_id = $model->product_id;
        $model->delete();

        return $this->redirect(['index', 'id'=> $product_id]);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionAddFundingProduct(){
        if(Yii::$app->request->isAjax){
            $fundingPlan= new FundingPlan();

            $qty_payments=$_POST['qty_payments'];
            $amount_payment=$_POST['amount_payment'];
            $product_id=$_POST['product_id'];

            $fundingPlan->amount_payment=$amount_payment;
            $fundingPlan->qty_payments=$qty_payments;
            $fundingPlan->save();
            $fundingPlan->link('products', \app\modules\sale\models\Product::findOne($product_id));
            
            $json=[];
            $json['status']='success';
            $json['html']= $this->renderAjax('_addfundingplan',[
                'fundingPlan'=>$fundingPlan,
            ]);
            //$json['idability']=$ability->idability;
            Yii::$app->response->format='json';
            return true;
        }
        else{
            throw new NotFoundHttpException('The requested page does not exist.'); 
        }
    }
    /**
     * Finds the FundingPlan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FundingPlan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FundingPlan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionTotals($id)
    {
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = 'json';
            $product = Product::findOne($id);
            $model = new FundingPlan();
            $model->load(Yii::$app->request->post());

            return [
                'finalAmount'=> Yii::$app->formatter->asCurrency($model->getFinalAmount() ),
                'finalTotalAmount'=> Yii::$app->formatter->asCurrency($model->getFinalTotalAmount() ),
                'finalTaxes'=> Yii::$app->formatter->asCurrency($model->getFinalTaxesAmount() )
            ];
        }
    }
}
