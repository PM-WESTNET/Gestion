<?php

namespace app\modules\employee\controllers;

use app\modules\employee\models\Employee;
use app\modules\employee\models\EmployeeBillHasTaxRate;
use app\modules\employee\models\EmployeeBillItem;
use Yii;
use app\modules\employee\models\EmployeeBill;
use app\modules\employee\models\search\EmployeeBillSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmployeeBillController implements the CRUD actions for EmployeeBill model.
 */
class EmployeeBillController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all EmployeeBill models.
     * @return mixed
     */
    public function actionIndex($employee_id=0)
    {
        $employee = null;
        $searchModel = new EmployeeBillSearch();
        if (empty($searchModel->start_date)) {
            $searchModel->start_date=(new \DateTime('now -1 month'))->format('d-m-Y');
        }
        if ($employee_id!=0) {
            $searchModel->employee_id = $employee_id;
            $employee = $this->findEmployee($employee_id);
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'employee'=>$employee
        ]);
    }

    /**
     * Displays a single EmployeeBill model.
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
     * Creates a new EmployeeBill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($employee=null, $from="index")
    {
        $model = new EmployeeBill();
        if ($employee) {
            $employee = $this->findEmployee($employee);
            $model->employee_id = $employee->employee_id;
            $model->type = $employee->bill_type;
        }

        if (empty($model->company_id)){
            $model->company_id = \app\modules\sale\models\Company::findDefault()->company_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $existing_employee_bill = EmployeeBill::find()->where(['number' => $model->number1.'-'.$model->number2])->andWhere(['employee_id' => $model->employee_id])->andWhere(['bill_type_id' => $model->bill_type_id])->one();
            if ($existing_employee_bill) {
                \Yii::$app->session->setFlash('error', 'Ya existe una factura con el mismo número para ese provedor');
                return $this->render('create', [
                            'model' => $model,
                            'dataProvider' => null,
                            'itemsDataEmployee' => null,
                            'from' => $from
                ]);
            } elseif ($model->save() && $model->validate()) {
                return $this->redirect(['employee-bill/update', 'id' => $model->employee_bill_id, 'from' => $from]);
            }
        }
            \app\components\helpers\FlashHelper::flashErrors($model);

            return $this->render('create', [
                        'model' => $model,
                        'dataProvider' => null,
                        'itemsDataEmployee' => null,
                        'from' => $from
            ]);

    }

    /**
     * Updates an existing EmployeeBill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $from="index")
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $pay_after_save = Yii::$app->request->post('pay_after_save');
            
            if ($pay_after_save == 'true'){
                return $this->redirect(['/employee/employee-payment/create', 'employee' => $model->employee_id, 'from' => $from]);
            } else if ($from=="account") {
                return $this->redirect(['employee/current-account', 'id' => $model->employee_id, 'from' => $from]);
            } else {
                return $this->redirect(['view', 'id' => $model->employee_bill_id, 'from' => $from]);
            }
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getEmployeeBillHasTaxRates(),
            ]);

            $itemsDataProvider = new ActiveDataProvider([
                'query' => $model->getEmployeeBillItems()
            ]);

            return $this->render('update', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'itemsDataProvider' => $itemsDataProvider,
                'from' => $from
            ]);
        }
    }

    /**
     * Deletes an existing EmployeeBill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $from="index")
    {
        $model = $this->findModel($id);
        $employee_id = $model->employee_id;
        if($model->delete()) {
            Yii::$app->session->addFlash('success', Yii::t('app', 'The Invoice is successfully deleted.'));
        } else {
            Yii::$app->session->addFlash('error', Yii::t('app', 'The Invoice could not be deleted.'));
        }

        if ($from=="account") {

            return $this->redirect(['employee/account', 'id'=> $employee_id]);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the EmployeeBill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeBill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmployeeBill::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findEmployee($id)
    {
        if (($model = \app\modules\employee\models\Employee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionAddTax($id)
    {
        $model = $this->findModel($id);
        $tax = new EmployeeBillHasTaxRate();
        $tax->load(Yii::$app->request->post());

        if($tax->validate()){
            $tax = $model->addTax([
                'employee_bill_id'  => $tax->employee_bill_id,
                'tax_rate_id'       => $tax->tax_rate_id,
                'amount'            => $tax->amount,
                'net'            => $tax->net
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillHasTaxRates(),
        ]);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillItems(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'itemsDataProvider' => $itemsDataProvider,
            'from' => 'index'
        ]);
    }

    /**
     * Deletes an existing AccountConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteTax($employee_bill_id, $tax_rate_id)
    {
        $modelDelete = EmployeeBillHasTaxRate::findOne([
            'employee_bill_id'=> $employee_bill_id,
            'tax_rate_id'=> $tax_rate_id]);
        if(!empty($modelDelete)) {
            $modelDelete->delete();
        }

        $model = $this->findModel($employee_bill_id);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillItems(),
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillHasTaxRates(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'from' => 'index',
            'itemsDataProvider' => $itemsDataProvider,
        ]);

    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionAddItem($id)
    {
        $model = $this->findModel($id);
        $item = new EmployeeBillItem();
        $item->load(Yii::$app->request->post());

        if($item->validate()){
            $item = $model->addItem([
                'employee_bill_id'  => $item->employee_bill_id,
                'account_id'        => $item->account_id,
                'description'       => $item->description,
                'amount'            => $item->amount
            ]);

            $model->calculateTotal();
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillHasTaxRates(),
        ]);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillItems(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'itemsDataProvider' => $itemsDataProvider,
            'from' => 'index'
        ]);
    }

    /**
     * Deletes an existing AccountConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($employee_bill_id, $employee_bill_item_id)
    {
        $modelDelete = EmployeeBillItem::findOne([
            'employee_bill_id'=> $employee_bill_id,
            'employee_bill_item_id'=> $employee_bill_item_id]);
        if(!empty($modelDelete)) {
            $modelDelete->delete();
        }

        $model = $this->findModel($employee_bill_id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillHasTaxRates(),
        ]);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillItems(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'itemsDataProvider' => $itemsDataProvider,
            'from' => 'index'
        ]);

    }

    public function actionListItems($employee_bill_id)
    {
        $this->layout = '//empty';
        $model = $this->findModel($employee_bill_id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillItems(),
        ]);

        return $this->render('_items', [
            'dataProvider' => $dataProvider
        ]);
    }
}
