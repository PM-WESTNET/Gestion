<?php

namespace app\modules\checkout\controllers;

use app\components\web\Controller;
use app\modules\checkout\components\PaymentPlanManager;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentPlan;
use app\modules\checkout\models\search\PaymentPlanSearch;
use app\modules\sale\models\Customer;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentPlanController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Payment models.
     * @return mixed
     */
    public function actionIndex($customer_id)
    {
        $customer = $this->findCustomer($customer_id);
        $dataProvider = new ActiveDataProvider([
            'query' => PaymentPlan::find()->where(['customer_id'=>$customer_id]),
        ]);

        return $this->render('index', [
            'customer' => $customer,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Utilizado para crear pago manual.
     * @return mixed
     */
    public function actionCreate($customer_id)
    {
        $customer = $this->findCustomer($customer_id);
        $model = new PaymentPlan();
        $model->customer_id = $customer_id;
        $payment = new Payment();
        $payment->customer_id = $customer_id;

        if( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            $model->save();
            $manager = new PaymentPlanManager($model);
            if ($manager->create()) {
                return $this->redirect(['/checkout/payment/current-account','customer' => $customer->customer_id]);
            }
        }

        return $this->render('create', [
            'customer' => $customer,
            'model' => $model,
            'payment' => $payment
        ]);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentPlan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentPlan::findOne($id)) !== null) {
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
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Cierra el pago, genera los movmientos contables
     *
     * @param $payment_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCancel($id, $customer_id)
    {
        $customer = $this->findCustomer($customer_id);
        $paymentPlan = $this->findModel($id);
        $manager = new PaymentPlanManager($paymentPlan);
        $manager->cancel();

        return $this->redirect(['index', 'customer_id' => $customer_id]);
    }
    
    public function actionList(){
        $search=new PaymentPlanSearch();
        error_log('Por entrar a search');
        $provider= $search->search(Yii::$app->request->getQueryParams());
        error_log('Sali de search');
        
        
        return $this->render('list', ['dataProvider' => $provider, 'search' => $search]);
    }

}
