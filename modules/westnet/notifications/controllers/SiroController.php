<?php

namespace app\modules\westnet\notifications\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\westnet\notifications\components\siro\ApiSiro;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\config\models\Config;
use app\modules\westnet\notifications\models\PaymentIntentionAccountability;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\checkout\models\PaymentMethod;
use app\modules\westnet\notifications\models\search\PaymentIntentionAccountabilitySearch;
use app\modules\westnet\notifications\models\SiroCompanyConfig;

/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class SiroController extends Controller
{
    /**
     * 
     */
    public function actionCheckerOfPayments()
    {
        $this->layout = '/fluid'; // sets no margin for this view
        if(Yii::$app->request->isPost){
            $request = Yii::$app->request->post();

            //
            if(isset($request['cierre_masivo'])) return $this->MassiveClosure();

            set_time_limit(7200); // 2 hr timeout

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // get company
                $company = Company::find()->where(['company_id' => $request['company_id']])->one();

                // example format -> '2022-09-08'
                $from_date = $request['date_from'];
                $to_date = $request['date_to'];

                // excecute revisor 
                $ok = PaymentIntentionAccountability::revisePaymentsProcess($company, $from_date, $to_date);

                // transaction commit
                if($ok){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                }
                
            } catch (\Exception $e) {
                file_put_contents(Yii::getAlias('@runtime/logs/log_contrastador.txt'),
                "Ha Ocurrido un error: \n" .
                "Hora: " . date('Y-m-d H:m:s') . "\n" .
                // "Respuesta de Siro: " . json_encode($accountability) . "\n" .
                "Error: " . json_encode($e) .
                "-----------------------------------------------------------------------------\n",
                FILE_APPEND);
                $transaction->rollBack();
            }
            
  
        }

        $searchModel = New PaymentIntentionAccountabilitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $collectionChannelNamesArr = $searchModel->getArrColletionChannelDescriptions();
        $companyNamesArr = Company::getArrCompanyNames();
        $statusArr = $searchModel->getArrStatus();
        $paymentMethodArr = $searchModel->getArrPaymentMethod();
        $companies_arr = SiroCompanyConfig::getEnabledCompanies();

        if (!empty($dataProvider)) {
            return $this->render(
                    'index',
                    [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'companyNamesArr' => $companyNamesArr,
                        'collectionChannelNamesArr' => $collectionChannelNamesArr,
                        'statusArr' => $statusArr,
                        'paymentMethodArr' => $paymentMethodArr,
                        'companies_arr' => $companies_arr,
                    ]
                );
        }


        return $this->render('index');
    }

    public function actionCancel($id){
        $model = PaymentIntentionAccountability::find()->where(['payment_intention_accountability_id' => $id])->one();
        $model->status = 'cancelled';
        $model->save();

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
    }

    public function actionConfirm($id){
        $transaction = Yii::$app->db->beginTransaction();
        $model = PaymentIntentionAccountability::find()->where(['payment_intention_accountability_id' => $id])->one();
        $customer = Customer::findOne(['customer_id' => $model->customer_id]);
        $payment_method = PaymentMethod::findOne(['name' => 'Botón de Pago']);

        $payment = new Payment([
            'customer_id' => $customer->customer_id,
            'amount' => $model->total_amount,
            'partner_distribution_model_id' => $customer->company->partner_distribution_model_id,
            'company_id' => $customer->company_id,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'status' => 'closed'
        ]);

        if ($payment->save(false)) {
            $payment_item = new PaymentItem();
            $payment_item->amount = $payment->amount;
            $payment_item->description = 'Intención de Pago (Banco Roela) ' . $model->siro_payment_intention_id;
            $payment_item->payment_method_id = $payment_method->payment_method_id;
            $payment_item->payment_id = $payment->payment_id;
            $payment_item->paycheck_id = null;
            
            $customer->current_account_balance -= $model->total_amount;

            $model->payment_id = $payment->payment_id;
            $model->status = 'payed';
            $model->save();

            $payment_item->save(false);
            $customer->save(false);

            $transaction->commit();
            Yii::$app->session->setFlash("success", "Se ha creado el pago correctamente.");

        } else {
            $transaction->rollBack();
            Yii::$app->session->setFlash("danger", "No se ha podido crear el pago.");
        }

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));
    }

    /**
     * Closes all the Payment Intentions found
     *
     */
    public function MassiveClosure(){
        $transaction = Yii::$app->db->beginTransaction();
        $models = PaymentIntentionAccountability::find()
            ->where(['status' => 'draft', 'payment_id' => null])
            ->andWhere(['not', ['siro_payment_intention_id' => null]])
            ->all();

        $payment_method = PaymentMethod::findOne(['name' => 'Botón de Pago']);
        try {
            foreach ($models as $key => $model) {
                $customer = Customer::findOne(['customer_id' => $model->customer_id]);
                $payment = new Payment([
                    'customer_id' => $customer->customer_id,
                    'amount' => $model->total_amount,
                    'partner_distribution_model_id' => $customer->company->partner_distribution_model_id,
                    'company_id' => $customer->company_id,
                    'date' => (new \DateTime('now'))->format('Y-m-d'),
                    'status' => 'closed'
                ]);

                if ($payment->save(false)) {
                    $payment_item = new PaymentItem();
                    $payment_item->amount = $payment->amount;
                    $payment_item->description = 'Intención de Pago (Banco Roela) ' . $model->siro_payment_intention_id;
                    $payment_item->payment_method_id = $payment_method->payment_method_id;
                    $payment_item->payment_id = $payment->payment_id;
                    $payment_item->paycheck_id = null;
                    
                    $customer->current_account_balance -= $model->total_amount;

                    $model->payment_id = $payment->payment_id;
                    $model->status = 'payed';
                    $model->save();

                    $payment_item->save(false);
                    $customer->save(false);
                }
            }
            Yii::$app->session->setFlash("success", "Se han creado los pagos correctamente.");
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash("danger", "No se han podido crear los pagos.");
        }

        return $this->redirect(Url::toRoute(['/westnet/notifications/siro/checker-of-payments']));  
    }


}
