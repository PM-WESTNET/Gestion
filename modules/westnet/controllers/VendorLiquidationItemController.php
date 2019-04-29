<?php

namespace app\modules\westnet\controllers;

use app\modules\westnet\components\VendorLiquidationService;
use Exception;
use Yii;
use app\modules\westnet\models\VendorLiquidationItem;
use app\modules\westnet\models\search\VendorLiquidationItemSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\helpers\UserA;

/**
 * VendorLiquidationItemController implements the CRUD actions for VendorLiquidationItem model.
 */
class VendorLiquidationItemController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all VendorLiquidationItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VendorLiquidationItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VendorLiquidationItem model.
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
     * Creates a new VendorLiquidationItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($liquidation_id)
    {
        $liquidation = \app\modules\westnet\models\VendorLiquidation::findOne($liquidation_id);
        if($liquidation === null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        if($liquidation->status != 'draft'){
            throw new Exception(Yii::t('westnet', 'This liquidation is closed and can not be updated.'));
        }
        
        $model = new VendorLiquidationItem();
        $model->vendor_liquidation_id = $liquidation->vendor_liquidation_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vendor_liquidation_item_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'liquidation' => $liquidation
            ]);
        }
    }

    /**
     * Updates an existing VendorLiquidationItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->vendorLiquidation->status != 'draft'){
            throw new Exception(Yii::t('westnet', 'This liquidation is closed and can not be updated.'));
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->vendor_liquidation_item_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'liquidation' => $model->vendorLiquidation
            ]);
        }
    }

    /**
     * Deletes an existing VendorLiquidationItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }
    
    
    /**
     * Anula un item de liquidacion, poniendo en 0 el importe.
     * @param integer $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        $description = $model->description;
        
        $model->cancel();
        
        Yii::$app->session->setFlash('success', Yii::t('westnet', 'Liquidation item {item} has been cancelled.', ['item' => $description.' | '.($model->contractDetail ? UserA::a($model->contractDetail->contract->customer->fullName,['/sale/customer/view', 'id' => $model->contractDetail->contract->customer->customer_id]) : NULL)]));

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the VendorLiquidationItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VendorLiquidationItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VendorLiquidationItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
