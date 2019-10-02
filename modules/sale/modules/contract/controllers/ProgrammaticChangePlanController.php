<?php

namespace app\modules\sale\modules\contract\controllers;

use app\components\web\Controller;
use app\modules\sale\models\Customer;
use app\modules\sale\modules\contract\models\Contract;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\sale\modules\contract\models\ProgrammaticChangePlan;
use app\modules\sale\modules\contract\models\search\ProgrammaticChangePlanSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProgrammaticChangePlanController implements the CRUD actions for ProgrammaticChangePlan model.
 */
class ProgrammaticChangePlanController extends Controller
{

    /**
     * Lists all ProgrammaticChangePlan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgrammaticChangePlanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgrammaticChangePlan model.
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
     * Creates a new ProgrammaticChangePlan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($contract_id = null, $customer_id = null)
    {
        $model = new ProgrammaticChangePlan();
        $customer = null;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->programmatic_change_plan_id]);
        }

        $queryPlans = \app\modules\sale\models\Product::find()
            ->andWhere(['type' => 'plan', 'status' => 'enabled']);

        if ($contract_id) {
            $contract = Contract::findOne($contract_id);

            if (empty($contract)) {
                throw new BadRequestHttpException('Contract not found');
            }
            $customer =  $contract->customer;

            $queryPlans->joinWith('categories');
            if ($customer->customerCategory->name === 'Familia') {
                $queryPlans->andWhere(['category.system' => 'planes-de-internet-residencial']);
            }elseif  ($customer->customerCategory->name === 'Empresa') {
                $queryPlans->andWhere(['category.system' => 'planes-de-internet-empresa']);
            }

            $model->contract_id = $contract_id;
        }

        if ($customer_id) {
            $customer = Customer::findOne($customer_id);

            if (empty($customer)) {
                throw new BadRequestHttpException('Customer not found');
            }

            $contract = Contract::findOne(['customer_id' => $customer->customer_id]);
            $queryPlans->joinWith('categories');
            if ($customer->customerCategory->name === 'Familia') {
                $queryPlans->andWhere(['category.system' => 'planes-de-internet-residencial']);
            }elseif  ($customer->customerCategory->name === 'Empresa') {
                $queryPlans->andWhere(['category.system' => 'planes-de-internet-empresa']);
            }

            $model->contract_id = $contract->contract_id;
        }

        $planes = \yii\helpers\ArrayHelper::map($queryPlans->all(),'product_id', 'name');

        return $this->render('create', [
            'model' => $model,
            'planes' => $planes,
            'customer' => $customer,
        ]);
    }

    /**
     * Updates an existing ProgrammaticChangePlan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->programmatic_change_plan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProgrammaticChangePlan model.
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
     * Finds the ProgrammaticChangePlan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgrammaticChangePlan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgrammaticChangePlan::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
