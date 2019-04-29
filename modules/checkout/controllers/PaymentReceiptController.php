<?php

namespace app\modules\checkout\controllers;

use Yii;
use app\modules\checkout\models\PaymentReceipt;
use app\modules\checkout\models\PaymentReceiptSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentReceiptController implements the CRUD actions for PaymentReceipt model.
 */
class PaymentReceiptController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all PaymentReceipt models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new \app\modules\checkout\models\search\PaymentReceiptSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentReceipt model.
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
     * Creates a new PaymentReceipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer, $method=0)
    {
        $model = new PaymentReceipt();
        
        $paymentSearch = new \app\modules\checkout\models\search\PaymentSearch();
        
        $customer = $this->findCustomer($customer);
        $model->customer_id = $customer->customer_id;
        $model->company_id = $customer->company_id;
        $paymentSearch->customer_id = $customer->customer_id;

        $method = null;
        if ($method!=0) {
            $method = $this->findMethod($method);
            $model->payment_method_id = $method->payment_method_id;
            $paymentSearch->payment_method_id = $method->payment_method_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            return $this->redirect(['payment/current-account', 'customer' => $customer->customer_id]);
            
        } else {
            return $this->render('create', [
                'model' => $model,
                'paymentSearch' => $paymentSearch
            ]);
        }
    }
    
    /**
     * Updates an existing PaymentReceipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['payment/current-account', 'customer' => $model->customer_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PaymentReceipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * If the return is specified, the browser will be redirected.
     * @param integer $id
     * @param string $return
     * @return mixed
     */
    public function actionDelete($id, $return="")
    {
        $model = $this->findModel($id);
        $model->delete();

        if (empty($return)) {
            return $this->redirect(['index']);
        } else {
            if($return=="account"){
                return $this->redirect(['payment/current-account', 'customer' => $model->customer_id]);
            }
        }
    }

    /**
     * Finds the PaymentReceipt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentReceipt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentReceipt::findOne($id)) !== null) {
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
    protected function findCustomer($id)
    {
        if (($model = \app\modules\sale\models\Customer::findOne($id)) !== null) {
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
    protected function findMethod($id)
    {
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
     */
    public function actionPdf($id)
    {
        Yii::$app->response->format = 'pdf';
        $model = $this->findModel($id);
        $this->layout = '//pdf';


        return ($this->render('pdf', [
            'model' => $model,
        ]));
    }
}
