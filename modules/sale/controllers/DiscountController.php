<?php

namespace app\modules\sale\controllers;

use app\modules\sale\models\Product;
use Yii;
use app\modules\sale\models\Discount;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app\components\web\Controller;
use app\modules\sale\models\CustomerHasDiscount;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\sale\models\search\DiscountSearch;

/**
 * DiscountController implements the CRUD actions for Discount model.
 */
class DiscountController extends Controller
{

    /**
     * Lists all Discount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';
        $dataProvider = new ActiveDataProvider([
            'query' => Discount::find(),
        ]);

        $searchModel = new DiscountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $apply_toArr = Discount::APPLY_TO_ENUM;
        $value_fromArr = Discount::VALUE_FROM_ENUM;
        $statusArr = Discount::STATUS_ENUM;
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'apply_toArr' => $apply_toArr,
            'value_fromArr' => $value_fromArr,
            'statusArr' => $statusArr,
        ]);
    }

    /**
     * Displays a single Discount model.
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
     * Creates a new Discount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Discount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->discount_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Discount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $enabled_referenced_discount = Discount::find()->where(['status' => Discount::STATUS_ENABLED, 'referenced' => 1])->all();
            if(count($enabled_referenced_discount) > 1) {
                Yii::$app->session->addFlash('error', Yii::t('app', 'More than one referenced discount are enabled'));
            }
            return $this->redirect(['view', 'id' => $model->discount_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Discount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();
        $model = $this->findModel($id);
        $customerDiscounts = (new CustomerHasDiscount)->getAllCustomersFromDiscount($id);
        if(!empty($customerDiscounts)){
            foreach($customerDiscounts as $discountOfCustomer){
                $discountOfCustomer->delete();
            }
        }
        try{
            // this only works if there are no bill_detail with the discount_id of deletion.
            $model->delete(); 
        }catch(\yii\db\IntegrityException $e){
            if(strpos(implode(" ",$e->errorInfo),"delete") !== false) Yii::$app->session->addFlash('error','No se pudo eliminar el descuento debido a que hay comprobantes que lo utilizan');
        }
        return $this->redirect(['index']);
    }


    /**
     * Finds the Discount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Discount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Discount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Retorna todos los descuentos por producto y si aplican a producto o customer
     */
    public function actionDiscountByProduct()
    {
        $out = [];
        $params = Yii::$app->request->post('depdrop_parents', null);
        $product_id  = $params[0];
        $is_customer = $params[1];
        if($params) {
            if($product_id) {
                $query = Product::findOne($product_id)
                    ->getDiscounts()
                    ->select(['discount_id as id', 'name']);
                if($is_customer!=-1) {
                    $query->where(['apply_to'=>($is_customer ? Discount::APPLY_TO_CUSTOMER : Discount::APPLY_TO_PRODUCT)]);
                }
                $out = $query->asArray()->all();
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }

        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }
}
