<?php

namespace app\modules\checkout\controllers;

use app\modules\checkout\models\BillHasPayment;
use app\modules\checkout\models\PaymentItem;
use app\modules\sale\models\search\BillSearch;
use Yii;
use app\modules\checkout\models\Payment;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\checkout\models\PaymentMethod;
use app\modules\checkout\models\search\PaymentSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\modules\checkout\models\PagoFacilTransmitionFile;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PaymentSearch();
        if (!isset($_GET['PaymentSearch'])) {
            $searchModel->from_date= date('Y-m').'-01';
            $searchModel->to_date= (new \DateTime())->modify('last day of this month')->format('Y-m-d');
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPayBill($bill, $payment = 0) {
        if (Yii::$app->request->get("_pjax", "") != "") {
            return;
        }

        $bill = $this->findBill($bill);

        $bill->complete();

        if (!$payment) {
            $bhp = BillHasPayment::findOne(['bill_id' => $bill->bill_id]);
            if (!$bhp) {
                $model = new Payment();
                $model->setAttributes($bill->getAttributes());
                $model->concept = "Pago de factura - " . $bill->number;
                $model->status = 'draft';
                $model->number = " " . $bill->number;
                $model->amount = $bill->total;
                $model->save();
                $model->applyToBill([ $bill->bill_id]);
            } else {
                $model = $bhp->payment;
            }
        } else {
            $model = $this->findModel($payment);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $bill->refresh();
                //Si la factura fue pagada por completo, continuamos a la vista
                if ($bill->getDebt() == 0) {
                    $model->close();
                    if (!$bill->billType->invoiceClass) {
                        $bill->close();
                    }
                    return $this->redirect(['/sale/bill/view', 'id' => $bill->bill_id]);
                }
            }
        }

        return $this->render('pay-bill', [
                    'model' => $model,
                    'bill' => $bill
        ]);
    }

    public function actionOpenBill($bill, $payment) {
        $this->findModel($payment)->delete();

        return $this->redirect(['/sale/bill/open', 'id' => $bill]);
    }

    public function actionCancelPayment($id) {
        $model = Payment::findOne($id);
        if ($model) {
            $model->status = 'cancelled';
            $model->save();
        }

        return $this->actionIndex();
    }

    /**
     * Utilizado para crear pago manual.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($customer = 0, $payment = 0) {
        $model = new Payment();
        $model->setScenario('manual');
        $customerObj = null;

        if ($customer != 0) {
            $customerObj = $this->findCustomer($customer);
            $model->customer_id = $customerObj->customer_id;
        }

        if ($payment != 0) {
            $model = $this->findModel($payment);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(!$model->date) {
                $model->date = (new \DateTime())->format('Y-m-d');
            }
            if (!is_object($customerObj) || !is_object($model)) {
                return $this->redirect(['create',
                            'customer' => $model->customer_id,
                            'payment' => $model->payment_id,
                ]);
            } else {
                return $this->redirect(['update',
                            'id' => $model->payment_id,
                ]);
            }
        }
        if(!$model->date) {
            $model->date = (new \DateTime())->format('Y-m-d');
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Utilizado para crear pago manual.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreateDebt($customer, $account) {
        $model = new Payment();
        $model->setScenario('manual');

        $customer = $this->findCustomer($customer);
        $model->customer_id = $customer->customer_id;
        $model->company_id = $customer->company_id;

        $method = $this->findMethod($account);
        $model->payment_method_id = $method->payment_method_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['current-account', 'customer' => $customer->customer_id]);
        }

        return $this->render('create-debt', [
                    'model' => $model,
        ]);
    }

    public function actionCurrentAccount($customer) {
        $this->layout = '//fluid';
        $dataModelAccount = null;
        $searchModelAccount = null;
        $customer = $this->findCustomer($customer);
        // Si se utiliza cuentas sacar la deuda con las cuentas
        if (Yii::$app->getModule("accounting")) {
            if ($customer->account !== null) {
                $searchModelAccount = new \app\modules\accounting\models\search\AccountMovementSearch();
                $searchModelAccount->account_id_from = $customer->account->lft;
                $searchModelAccount->account_id_to = $customer->account->rgt;
                $dataModelAccount = $searchModelAccount->search(Yii::$app->request->queryParams);
            }
        }
        $searchModel = new PaymentSearch();
        $searchModel->customer_id = $customer->customer_id;

        $dataProvider = $searchModel->searchAccount($customer->customer_id, Yii::$app->request->queryParams);

        $retVals = [
            'searchModel' => $searchModel,
            'customer' => $customer,
            'dataProvider' => $dataProvider
        ];
        $retVals['dataModelAccount'] = $dataModelAccount;
        $retVals['searchModelAccount'] = $searchModelAccount;

        return $this->render('account', $retVals);
    }

    /**
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->payment_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $return
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id, $return = "") {
        $model = $this->findModel($id);
        $model->delete();

        if (empty($return)) {
            return $this->redirect(['index']);
        } else {
            if ($return == "account") {
                return $this->redirect(['payment/current-account', 'customer' => $model->customer_id]);
            }
        }
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBill($id) {
        if (($model = Bill::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCustomer($id) {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the PaymentMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentMethod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findMethod($id) {
        if (($model = \app\modules\checkout\models\PaymentMethod::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Prints the pdf of a single Bill.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPdf($id) {
        //Yii::$app->response->format = 'pdf';
        $model = $this->findModel($id);
        $this->layout = '//pdf';


        return ($this->render('pdf', [
                    'model' => $model,
        ]));
    }

    /**
     * Agrega un item al pago
     *
     * @param int $id
     * @return json
     * @throws NotFoundHttpException
     */
    public function actionAddItem($id) {
        $status = 'error';
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);

        $item = new PaymentItem();
        $item->payment_id = $id;
        $item->load(Yii::$app->request->post());

        if (($model->calculateTotalItems() + $item->amount) > $model->amount) {
            return [
                'status' => 'error',
                'errors' => [
                    'amount' => Yii::t('app', 'The total of items is greater than the payment.')
                ]
            ];
        }

        if ($item->validate() && $item->save()) {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error',
                'errors' => $item->getErrors()
            ];
        }
    }

    /**
     * Borra un item de pago
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($payment_item_id) {
        Yii::$app->response->format = 'json';
        $status = 'error';
        if (PaymentItem::findOne($payment_item_id)->delete()) {
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
     * Agrega un item al pago
     *
     * @param int $id
     * @return json
     */
    public function actionAddBill($id) {
        $status = 'error';
        $message = '';
        Yii::$app->response->format = 'json';

        $bills_ids = Yii::$app->request->post('bills');
        $model = $this->findModel($id);
        if ($model->applyToBill($bills_ids)) {
            $status = 'success';
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
    public function actionRemoveBill($id) {
        $status = 'error';
        $message = '';
        Yii::$app->response->format = 'json';

        $bills_ids = Yii::$app->request->post('bills');
        $model = $this->findModel($id);
        if ($model->disengageBill($bills_ids)) {
            $status = 'success';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Borra un item de pago
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteBill($id) {
        Yii::$app->response->format = 'json';
        $status = 'error';
        if (BillHasPayment::findOne()->delete($id)) {
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
     * Cierra el pago, genera los movmientos contables
     *
     * @param $payment_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionClose($payment_id) {
        $model = $this->findModel($payment_id);
        if ($model->close()) {
            return $this->redirect(['view', 'id' => $model->payment_id]);
        } else {
            return $this->redirect(['update', 'id' => $model->payment_id]);
        }
    }

    /**
     * Muestra la pantalla para poder aplicar el pago a comprobantes
     *
     * @param $payment_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionApply($id) {
        $model = $this->findModel($id);
        $searchModel = new BillSearch;
        $searchModel->customer_id = $model->customer_id;
        $billDataProvider = $searchModel->searchWithDebt([]);

        $appliedDataProvider = new ActiveDataProvider([
            'query' => $model->getBillHasPayments(),
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

    public function actionPagofacilPaymentsImport() {

        $transmition_file = new PagoFacilTransmitionFile();

        if ($transmition_file->load(Yii::$app->request->post()) && $this->upload($transmition_file, 'file')) {
            $import = $transmition_file->import();
            if($import['status']) {
                Yii::$app->session->setFlash('success', 'Archivo importado con éxito');
                return $this->redirect(['pagofacil-payment-view', 'idFile' => $transmition_file->pago_facil_transmition_file_id]);
            } else {
                unlink(Yii::getAlias('@webroot') . '/'.$transmition_file->file_name);
                $transmition_file->delete();
                $string_error = '';
                foreach ($import['errors'] as $error) {
                    $string_error .= $error . "<br>";
                }
                Yii::$app->session->setFlash('error', Yii::t('app', 'An error occurred while importing file: ')."<br>".$string_error);
            }
        }

        return $this->render('pagofacil-payments-import', ['model'=> $transmition_file]);
    }
    
    public function actionPagofacilPaymentsIndex(){
        
        $pagofacilFiles= PagoFacilTransmitionFile::find()->orderBy(['pago_facil_transmition_file_id' => SORT_DESC]);
        
        $dataProvider = new ActiveDataProvider(['query' => $pagofacilFiles]);
        
        return $this->render('pagofacil-payments-index', ['dataProvider' => $dataProvider]);
    }
    
    public function actionPagofacilPaymentView($idFile){
        $model= \app\modules\checkout\models\PagoFacilTransmitionFile::findOne(['pago_facil_transmition_file_id' => $idFile]);
       
        $payments= new ActiveDataProvider(['query' => $model->payments()]);
        
        return $this->render('pagofacil-payment-view', [
            'model' => $model,
            'payments' => $payments,
        ]);
    }
    
    public function actionConfirmFile($idFile){
        set_time_limit(300);
        $model = PagoFacilTransmitionFile::findOne(['pago_facil_transmition_file_id' => $idFile]);
        
        if ($model->confirmFile()) {
            Yii::$app->session->setFlash('success', 'Archivo de pagos confirmado con éxito');
            Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;       
            return ['status'=> 'ok'];
    
        }else{
            Yii::$app->session->setFlash('error', 'Problema al confirmar archivo de pagos');
            Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;       
            return ['status'=> 'fail'];
    
        }
    }
    
    
    public function actionChangeCustomer()
    {
        $payment_id= \Yii::$app->request->post('payment_id');
        $new_customer_id= \Yii::$app->request->post('customer_id');
        
        $payment= $this->findModel($payment_id);
        
        $payment->customer_id= $new_customer_id;
        
        if ($payment->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Customer changed successfull'));
            $this->redirect(['view', 'id' => $payment->payment_id]);
        }else{
            \Yii::$app->session->setFlash('error', \Yii::t('app', 'Can`t change the Customer'));
            $this->redirect(['view', 'id' => $payment->payment_id]);
        }
    }

    /**
     * @param $model
     * @param $attribute
     * @return bool
     * @throws \Exception
     * Sube un archivo de pago facil, primero se fija que no haya sido importado previamente.
     */
    public function upload($model, $attribute)
    {
        $file = UploadedFile::getInstance($model, $attribute);
        $folder = date('Y').'/'.date('m');
        if ($file) {
            $filePath = Yii::$app->params['upload_directory'] . "$folder/". $file->baseName . '.' . $file->extension;

            $model->file_name = $filePath;
            $model->upload_date = (new \DateTime('now'))->format('Y-m-d');

            if(!$model->isRepeat()) {
                if (!file_exists(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/")) {
                    mkdir(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/", 0775, true);
                }

                $file->saveAs(Yii::getAlias('@webroot') . '/' . $filePath);
                return $model->save(false);
            }

            return false;
        } else {
            return false;
        }
    }

    public function actionDeletePagoFacilTransmitionFile($id) {
        $model = PagoFacilTransmitionFile::findOne($id);
        if(!$model) {
            Yii::$app->setFlash('error', 'No es posible encontrar el modelo');
        }

        if($model->getDeletable()) {
            if($model->file_name && file_exists(Yii::getAlias('@webroot') . '/' .$model->file_name)){
                unlink(Yii::getAlias('@webroot') . '/'.$model->file_name);
            }
            $model->delete();
        } else {
            Yii::$app->setFlash('error', 'No es posible eliminar el archivo');
        }

        return $this->redirect(['pagofacil-payments-index']);
    }
}
