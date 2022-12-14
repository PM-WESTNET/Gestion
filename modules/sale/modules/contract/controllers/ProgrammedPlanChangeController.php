<?php

namespace app\modules\sale\modules\contract\controllers;

use app\components\web\Controller;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Product;
use app\modules\sale\modules\contract\models\Contract;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\sale\modules\contract\models\ProgrammedPlanChange;
use app\modules\sale\modules\contract\models\search\ProgrammedPlanChangeSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\sale\modules\contract\models\Plan;

/**
 * ProgrammedPlanChangeController implements the CRUD actions for ProgrammedPlanChange model.
 */
class ProgrammedPlanChangeController extends Controller
{

    /**
     * Lists all ProgrammedPlanChange models.
     * @return mixed
     */
    public function actionIndex($customer_id = null)
    {
        $searchModel = new ProgrammedPlanChangeSearch();
        if($customer_id != null) {
            $searchModel->customer_id = $customer_id;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgrammedPlanChange model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProgrammedPlanChange model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($contract_id = null, $customer_id = null)
    {
        $model = new ProgrammedPlanChange([
            'user_id' => Yii::$app->user->getId()
        ]);
        $customer = null;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('app','Programmed Plan Change created successfull'));
            return $this->redirect(['index']);
        }

        if ($contract_id) {
            $contract = Contract::findOne($contract_id);

            if (empty($contract)) {
                throw new BadRequestHttpException('Contract not found');
            }
            $customer =  $contract->customer;
            $model->customer_id = $customer->customer_id;
            $model->contract_id = $contract_id;
        }

        if ($customer_id) {
            $customer = Customer::findOne($customer_id);

            if (empty($customer)) {
                throw new BadRequestHttpException('Customer not found');
            }

            $contract = Contract::findOne(['customer_id' => $customer->customer_id]);
            $model->customer_id = $customer->customer_id;
            $model->contract_id = $contract->contract_id;
        }

        if (Yii::$app->user->identity->hasRole('seller', false)) {
            $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'seller-plan'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');
            }else if(Yii::$app->user->identity->hasRole('internal-seller', false)){
                $subQueryplans = Product::find()
                    ->distinct()
                    ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'plan-para-vendedores-internos'])
                    ->andWhere(['or',
                        ['company_id'=>$customer->parent_company_id],
                        ['company_id'=>null]
                    ])
                    ->orderBy('product.name');
  
            }else if(Yii::$app->user->identity->hasRole('external-seller', false)){
                        $subQueryplans = Product::find()
                        ->distinct()
                        ->where(['product.type' => 'plan', 'product.status' => 'enabled'])
                        ->joinWith('categories')
                        ->andWhere(['category.system' => 'plan-para-vendedores-externos'])
                        ->andWhere(['or',
                            ['company_id'=>$customer->parent_company_id],
                            ['company_id'=>null]
                        ])
                        ->orderBy('product.name');           
            } else {
                $subQueryplans = Product::find()
                        ->where(['type'=>'plan', 'status' => 'enabled' ])
                        ->andWhere(['or',
                            ['company_id'=>$customer->parent_company_id],
                            ['company_id'=>null]
                        ])
                        ->distinct()
                        ->orderBy('product.name');
                        
            }
        if ($customer->customerCategory->name === 'Familia') {
            $queryplans = Product::find()
                    ->from(['sub' => $subQueryplans])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'planes-de-internet-residencial'])
                    ->orderBy('sub.name');
            $plans = $queryplans->all();  
            
        }elseif($customer->customerCategory->name === 'Empresa'){
             $queryplans = Product::find()
                    ->from(['sub' => $subQueryplans])
                    ->joinWith('categories')
                    ->andWhere(['category.system' => 'planes-de-internet-empresa'])
                    ->orderBy('sub.name');
            $plans = $queryplans->all();  
        }else{
            $plans= $subQueryplans->all();
        }

        return $this->render('create', [
            'model' => $model,
            'customer' => $customer,
            'contract_id' => $contract_id,
            'plans' => $plans
        ]);
    }

    /**
     * Updates an existing ProgrammedPlanChange model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->programmed_plan_change_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProgrammedPlanChange model.
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
     * Finds the ProgrammedPlanChange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgrammedPlanChange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgrammedPlanChange::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
