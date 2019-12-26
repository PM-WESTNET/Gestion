<?php

namespace app\modules\sale\modules\contract\controllers;

use app\modules\sale\models\Category;
use app\modules\sale\models\Customer;
use Yii;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\sale\modules\contract\models\search\PlanSearch;
use app\modules\sale\models\search\ProductSearch;
use app\components\web\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\sale\models\ProductPrice;
use app\modules\sale\models\search\ProductPriceSearch;
use yii\web\Response;

/**
 * PlanController implements the CRUD actions for Plan model.
 */
class PlanController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Plan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';
        
        $searchModel = new PlanSearch();
        if(empty($_GET['PlanSearch']['search_text'])){
            $searchModel = new PlanSearch;
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        }else{
            $dataProvider = $searchModel->searchText(Yii::$app->request->getQueryParams());
        }
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Plan model.
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
     * Creates a new Plan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Plan;
        $price = new ProductPrice;
        

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $price->load(Yii::$app->request->post());
            $model->setPrice($price->net_price, $price->exp_date);  
            
            return $this->redirect(['view', 'id' => $model->product_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'price' =>$price
            ]);
        }
    }

    /**
     * Updates an existing Plan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $price = $model->activePrice;
        
        if(empty($price)) $price = new ProductPrice;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            //Nuevo precio
            $newPrice = new ProductPrice;
            $newPrice->load(Yii::$app->request->post());
            
            //Si el nuevo precio difiere del anterior, actualizamos. TODO: funcion
            if($newPrice->arePlanValuesChanged($model->calculateTaxes($newPrice->net_price), $price)){
                $last_new_price = $model->setPrice($newPrice->net_price, $newPrice->exp_date);
                $last_new_price->updateAttributes(['future_final_price' => $newPrice->future_final_price]);
            }

            return $this->redirect(['view', 'id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'price' => $price
            ]);
        }
    }
    
    
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->product_id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }
    
    public function actionUpdatePrices($type='product')
    {
        if($type == 'plan'){
            $searchModel = new PlanSearch;
        }else{
            $searchModel = new ProductSearch;
        }
        
        $reflex = new \ReflectionClass($searchModel);
        
        if(empty($_GET[$reflex->getShortName()]['search_text'])){
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        }else{
            $dataProvider = $searchModel->searchText(Yii::$app->request->getQueryParams());
        }

        return $this->render('update-prices', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        
    }
    
    public function actionPriceHistory($id){
        
        $model = $this->findModel($id);
        $searchModel = new ProductPriceSearch;
        
        $dataProvider = new \yii\data\ActiveDataProvider(
            [
                'query'=>$model->getProductPrices(),
                'sort'=>[
                    'defaultOrder'=>['product_price_id'=>SORT_DESC]
                ]
            ]
        );
        
        return $this->render('price-history', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
        
    }

    
    public function actionUpdatePrice(){
        
        $id = Yii::$app->request->post('model_id');
        
        $model = Plan::findOne($id);
        
        $json = [];
        
        if($model === null){
            
            $json = ['status'=>'error','errors'=>'Not found.'];
            
        }else{
            
            $product = Yii::$app->request->post('Plan');
            $net_price = isset($product['netPrice']) ? $product['netPrice'] : 0;
            
            $model->setPrice($net_price, null);
            
            //Si el nuevo precio difiere del anterior, actualizamos
            $json = ['status'=>'success'];
                
            
        }
        
        echo \yii\helpers\Json::encode($json);
        
    }
    /**
     * Deletes an existing Plan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    
    public function actionBatchUpdater(){
        
        $model = new \app\modules\sale\models\UpdatePriceFormModel();
        
        if($model->load(Yii::$app->request->post())){
            
            $count = Plan::batchUpdate($model->percentage, 'plan',$model->filter, $model->exp_date, $model->items, $model->category);
            
            $json = [
                'status'=>'success',
                'count'=>$count
            ];
            
            //No funciona con session->setFlash porque la pagina luego recarga el contenedor con pjax.
            Yii::$app->session->set('updater-messages',['success'=>Yii::t('app','Plan prices has been updated succesfully. {count} plans updated.',['count'=>$count])]);
            
            echo \yii\helpers\Json::encode($json);
            
            return;
            
        }
        
        echo $this->renderAjax('_batch-updater',[
            'model'=>$model
        ]);
        
    }
    /**
     * Finds the Plan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Plan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Plan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Devuelve un array con planes para ser listados en un select2
     */
    public function actionGetPlansByCustomer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $customer_id = $parents[0];
                $customer = Customer::findOne($customer_id);

                if(!$customer) {
                    throw new BadRequestHttpException('Customer not found');
                }

                $fibra_category = Category::findOne(['system' => 'plan-fibra']);
                $wifi_category = Category::findOne(['system' => 'plan-wifi']);
                $fibra_products = (new Query())->select('product.product_id')
                    ->from('product')
                    ->leftJoin('product_has_category phc', 'phc.product_id = product.product_id')
                    ->where(['type' => Plan::TYPE])
                    ->andWhere(['phc.category_id' => $fibra_category->category_id]);
                $wifi_products = (new Query())->select('product.product_id')
                    ->from('product')
                    ->leftJoin('product_has_category phc', 'phc.product_id = product.product_id')
                    ->where(['type' => Plan::TYPE])
                    ->andWhere(['phc.category_id' => $wifi_category->category_id]);

                if($customer->hasFibraPlan()) {

                    $queryPlans = Plan::find()->andWhere(['product.status' => 'enabled']);
                    $queryPlans->joinWith('categories')
                        ->where(['in','product.product_id', $fibra_products]);
                } else {

                    $queryPlans = Plan::find()->andWhere(['product.status' => 'enabled']);
                    $queryPlans->joinWith('categories')
                        ->where(['in','product.product_id', $wifi_products]);
                }

                $customer_category = $customer->customerCategory;

                if ($customer_category->name == 'Familia') {
                    $queryPlans->andWhere(['category.system' => 'planes-de-internet-residencial'])
                    ->andWhere(['product.type' => Plan::TYPE]);
                }elseif  ($customer_category->name == 'Empresa') {
                    $queryPlans->andWhere(['category.system' => 'planes-de-internet-empresa'])
                        ->andWhere(['product.type' => Plan::TYPE]);
                }

                foreach ($queryPlans->all() as $plan) {
                    $out[] = ['id' => $plan->product_id, 'name' => $plan->name];
                }

                return ['output'=>$out, 'selected'=>''];
            }
        }
    }
}
