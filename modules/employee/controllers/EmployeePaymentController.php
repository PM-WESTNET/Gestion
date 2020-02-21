<?php

namespace app\modules\employee\controllers;

use app\modules\employee\models\Employee;
use app\modules\employee\models\EmployeeBill;
use app\modules\employee\models\EmployeeBillHasEmployeePayment;
use app\modules\employee\models\EmployeePayment;
use app\modules\employee\models\EmployeePaymentItem;
use app\modules\employee\models\ProviderBill;
use app\modules\employee\models\ProviderBillHasProviderPayment;
use app\modules\employee\models\ProviderPaymentItem;
use app\modules\employee\models\search\EmployeeBillSearch;
use app\modules\employee\models\search\EmployeePaymentSearch;
use app\modules\employee\models\search\ProviderBillSearch;
use Yii;
use app\modules\employee\models\ProviderPayment;
use app\modules\employee\models\search\ProviderPaymentSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\modules\accounting\models\MoneyBox;
use app\modules\config\models\Config;

/**
 * ProviderPaymentController implements the CRUD actions for ProviderPayment model.
 */
class EmployeePaymentController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all ProviderPayment models.
     * @return mixed
     */
    public function actionIndex($employee_id = 0) {
        $employee = null;
        $searchModel = new EmployeePaymentSearch();
        if ($employee_id != 0) {
            $searchModel->employee_id = $employee_id;
            $employee = $this->findEmployee($employee_id);
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'employee' => $employee
        ]);
    }

    /**
     * Displays a single ProviderPayment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProviderPayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($employee = 0, $employee_payment = 0) {
        $model = new EmployeePayment();
        if ($employee_payment != 0) {
            $model = $this->findModel($employee_payment);
        }

        if ($employee != 0) {
            $employee = $this->findEmployee($employee);
            $model->employee_id = $employee->employee_id;
        }

        if (empty($model->company_id)){
            $model->company_id = \app\modules\sale\models\Company::findDefault()->company_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['update', 'id' => $model->employee_payment_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Updates an existing ProviderPayment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save() ) {
            if( $model->verifyItems() ) {
                if($model->status=='closed'&&$model->canClose()) {
                    return $this->redirect(['view', 'id' => $model->employee_payment_id]);
                }
            }
            Yii::$app->session->addFlash('error', Yii::t('app', 'The Money Box Account is Closed. Change the date of the Payment.'));
        }
        $search = new EmployeePaymentSearch();
        $billDataProvider = new ActiveDataProvider([
            'query' =>$search->searchPendingBills($model->employee_id, $model->employee_payment_id)
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBillHasEmployeePayments(),
        ]);
        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'billDataProvider' => $billDataProvider
        ]);
    }

    /**
     * Cierra el pago
     * @param integer $id
     * @return mixed
     */
    public function actionClose($id) {
        $model = $this->findModel($id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if($model->canClose()) {
            $model->status = 'closed';
            $model->save();
            return $this->redirect(['view', 'id' => $model->employee_payment_id]);
        } else {
            return $this->redirect(['update', 'id' => $model->employee_payment_id]);
        }
    }

    /**
     * Deletes an existing ProviderPayment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $from = "index") {

        $payment = $this->findModel($id);
        $employee_id = $payment->employee_id;
        $payment->delete();

        if ($from == "account") {
            return $this->redirect(['employee/account', 'id' => $employee_id]);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the ProviderPayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeePayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = EmployeePayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Provider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findEmployee($id) {
        if (($model = \app\modules\employee\models\Employee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Provider Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmployeeBill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBill($id) {
        if (($model = \app\modules\employee\models\EmployeeBill::findOne($id)) !== null) {
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
    public function actionAddBill($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $bill = new EmployeeBillHasEmployeePayment();
        $bill->load(Yii::$app->request->post());

        if ($bill->validate()) {

            $model = $this->findModel($id);
            $bill = $model->addBill([
                'employee_bill_id' => $bill->employee_bill_id,
                'employee_payment_id' => $bill->employee_payment_id,
                'amount' => $bill->amount
            ]);

            return [
                'status' => 'success',
                'detail' => $bill
            ];
        } else {

            return [
                'status' => 'error',
                'errors' => \yii\widgets\ActiveForm::validate($bill)
            ];
        }
    }

    /**
     * Deletes an existing AccountConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteBill($employee_bill_id, $employee_payment_id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $modelDelete = EmployeeBillHasEmployeePayment::findOne([
                    'employee_bill_id' => $employee_bill_id,
                    'employee_payment_id' => $employee_payment_id]);
        if (!empty($modelDelete)) {
            $modelDelete->delete();
        }
        return [
            'status' => 'success',
        ];
    }


    /**
     * Agrega un item al pago
     *
     * @param int $id
     * @return json
     */
    public function actionAddItem($id) {
        $status = 'error';
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);

        $item = new EmployeePaymentItem();
        $item->load(Yii::$app->request->post());

        if($item->moneyBoxAccount) {
            if($item->moneyBoxAccount->small_box)  {
                $date = new \DateTime($item->moneyBoxAccount->daily_box_last_closing_date);
                $modelDate = new \DateTime($model->date);
                if($modelDate <= $date ) {
                    return [
                        'status' => 'error_account',
                        'errors' => Yii::t('app', 'The Money Box Account is Closed. Change the date of the Payment.')
                    ];
                }
            }
        }


        if($item->validate()) {
            $item = $model->addItem([
                'employee_payment_item_id' => $item->employee_payment_item_id,
                'employee_payment_id' => $item->employee_payment_id,
                'description' => $item->description,
                'number' => $item->number,
                'amount' => $item->amount,
                'payment_method_id' => $item->payment_method_id,
                'paycheck_id' => $item->paycheck_id,
                'money_box_account_id' => $item->money_box_account_id,
            ]);
            return [
                'status' => 'success',
                'detail' => $item
            ];
        } else {

            return [
                'status' => 'error',
                'errors' => \yii\widgets\ActiveForm::validate($item)
            ];
        }
    }


    /**
     * Borra un item de pago
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($employee_payment_item_id) {
        Yii::$app->response->format = 'json';
        $status = 'error';
        if (EmployeePaymentItem::findOne($employee_payment_item_id)->delete()) {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error',
            ];
        }
    }

    /**
     * Borra un item de pago
     *
     * @param integer $employee_payment_id
     * @param date $date
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdateDate() {
        Yii::$app->response->format = 'json';
        $employee_payment_id = Yii::$app->request->post('employee_payment_id');
        $date = Yii::$app->request->post('date');
        $status = 'error';
        if (($model = EmployeePayment::findOne($employee_payment_id))!==false) {

            if($model->verifyItems($date)) {
                $model->date = $date;
                $model->update(false);

                return [
                    'status' => 'success'
                ];
            } else {
                return [
                    'status' => 'error',
                    'date' => (new \DateTime($model->date))->format('d-m-Y')
                ];
            }
        } else {
            return [
                'status' => 'error',
                'date' => (new \DateTime($model->date))->format('d-m-Y')
            ];
        }
    }

    /**
     * Vista que permite aplicar el pago a un comprobante
     * @param $payment_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionApply($employee_payment_id) {
        $model = EmployeePayment::findOne($employee_payment_id);
        $searchModel = new EmployeeBillSearch();
        $searchModel->employee_id = $model->employee_id;
        $billDataProvider = $searchModel->searchWithDebt([]);

        $appliedDataProvider = new ActiveDataProvider([
            'query' => $model->getEmployeeBills(),
            'sort' => [
                //'defaultOrder' => ['bill.date'=>SORT_ASC]
            ]
        ]);

        return $this->render('apply', [
            'model' => $model,
            'billDataProvider' => $billDataProvider,
            'appliedDataProvider' => $appliedDataProvider
        ]);
    }

    /**
     * Agrega un item al pago
     *
     * @param int $id
     * @return json
     */
    public function actionAsociateProviderBill($id) {
        $status = 'success';
        $message = '';
        Yii::$app->response->format = 'json';

        $bills_ids = Yii::$app->request->post('bills');
        $model = $this->findModel($id);
        if (!$model->associateEmployeeBills($bills_ids)) {
            $message = Yii::t('app', 'One or more employee bills cant be applied correctly');
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Agrega un item al pago
     *
     * @param int $id
     * @return json
     */
    public function actionRemoveAssociationWithProviderBill($id) {
        $status = 'success';
        $message = '';
        Yii::$app->response->format = 'json';

        $bills_ids = Yii::$app->request->post('bills');
        $model = $this->findModel($id);
        if (!$model->disassociateEmployeeBills($bills_ids)) {
            $message = Yii::t('app', 'One or more employee bills cant be disassociated');
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Devuelve un listado de bancos o cajas para ser desplegados en un selector.
     */
    public function actionGetDataForSelector($type) {
        Yii::$app->response->format = 'json';
        $data = [];

        if($type == 'bank'){
            foreach (MoneyBox::findByMoneyBoxType(Config::getValue('money_box_bank'))->all() as $bank) {
                array_push($data, ['val' => $bank->money_box_id, 'text' => $bank->name]);
            }
        } else {
            foreach (MoneyBox::findByMoneyBoxType(Config::getValue('money_box_smallbox'))->all() as $box) {
                array_push($data, ['val' => $box->money_box_id, 'text' => $box->name]);
            }
        }

        return $data;
    }
}
