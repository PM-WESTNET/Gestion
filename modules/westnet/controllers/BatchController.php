<?php

namespace app\modules\westnet\controllers;

use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\models\Discount;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\westnet\components\SecureConnectionUpdate;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\search\CustomerContractSearch;
use Yii;
use app\modules\westnet\models\IpRange;
use app\modules\westnet\models\search\IpRangeSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/*
 * BachController
 * Estan concentrados los procesos batch a realizar con clientes, planes y demas.
 *
 */
class BatchController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }


    /**
     * Filtro de customers para asignacion masiva de planes.
     *
     * @return string
     */
    public function actionPlansToCustomer()
    {
        $searchModel = new CustomerContractSearch();

        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->search(Yii::$app->request->getQueryParams())
        ]);

        return $this->render('plan-to-customer', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Se asignan los planes al customer
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPlanToCustomerAssign()
    {
        set_time_limit(0);
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = 'json';

            $new_product_id = Yii::$app->request->post('CustomerContractSearch')['new_product_id'];
            $searchModel = new CustomerContractSearch();
            /** @var Query $query */
            $query = $searchModel->search(Yii::$app->request->getBodyParams());


            // Seteo la cantidad en la session
            $total = $query->count();
            Yii::$app->session->set( '_batch_to_customer_', [
                'total' => $total,
                'qty' => 0
            ]);
            $i = 1;
            foreach ( $query->all() as $key => $value) {
                $contractDetail = ContractDetail::findOne(['contract_detail_id'=>$value['contract_detail_id']]);
                $contractDetailOld = clone($contractDetail);
                $contractDetail->product_id = $new_product_id;

                // Si es distinto lo guardo
                if (!$contractDetail->isEqual($contractDetailOld) &&  $contractDetail->status != Contract::STATUS_DRAFT) {
                    // Pongo la fecha de fin en el viejo para poder crear el log
                    $contractDetailOld->to_date = (new \DateTime('now'))->modify('-1 day')->format('d-m-Y');
                    $contractDetailOld->createLog();
                    // Pongo nueva fecha de inicio y guardo
                    $contractDetail->from_date = (new \DateTime('now'))->format('d-m-Y');
                    $contractDetail->applied = false;
                    $contractDetail->save(false,['from_date', 'status', 'product_id', 'applied']);

                    // Actualizo la conexion
                    /*$connection = Connection::findOne(['connection_id'=>$value['connection_id']]);
                    $contract   = Contract::findOne(['contract_id'=>$value['contract_id']]);
                    SecureConnectionUpdate::update($connection, $contract, false);*/
                }

                // Seteo la cantidad en la session
                Yii::$app->session->set( '_batch_to_customer_', [
                    'total' => $total,
                    'qty' => $i
                ]);
                Yii::$app->session->close();
                $i++;
            }

            return [
                'status' => 'success',
                'messages' => [],
                'total' => $total,
            ];
        }
    }

    /**
     * Filtro de customers para asignacion masiva de company.
     *
     * @return string
     */
    public function actionCompanyToCustomer()
    {
        $searchModel = new CustomerContractSearch();

        /** @var Query $query */
        $query = $searchModel->search(Yii::$app->request->getQueryParams());
        /** @var Query $queryCount */
        $queryCount = clone $query;
        $count = $queryCount->count('distinct c.customer_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount' => $count
        ]);

        return $this->render('company-to-customer', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Se asignan las company al customer
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCompanyToCustomerAssign()
    {
        set_time_limit(0);
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = 'json';

            $new_company_id = Yii::$app->request->post('CustomerContractSearch')['new_company_id'];
            $searchModel = new CustomerContractSearch();
            /** @var Query $query */
            $query = $searchModel->search(Yii::$app->request->getBodyParams());

            // Seteo la cantidad en la session
            /** @var Query $queryCount */
            $queryCount = clone $query;
            $total = $queryCount->count('distinct c.customer_id');
            Yii::$app->session->set( '_batch_to_customer_', [
                'total' => $total,
                'qty' => 0,
                'message' => []
            ]);
            $i = 1;
            foreach ( $query->all() as $key => $value) {
                /** @var Customer $customer */
                $customer = Customer::findOne(['customer_id'=>$value['customer_id']]);
                $customer->company_id = $new_company_id;
                $customer->validate();
                if(!count($customer->getErrors())){
                    $customer->save();
                    // Seteo la cantidad en la session
                    Yii::$app->session->set( '_batch_to_customer_', [
                        'total' => $total,
                        'qty' => $i,
                        'messages' => []
                    ]);
                    Yii::$app->session->close();
                    $i++;
                } else {
                    Yii::$app->session->set( '_batch_to_customer_', [
                        'total' => $total,
                        'qty' => 0,
                    ]);
                    $messages = Yii::$app->session->get( '_batch_to_customer_messages_');
                    $message = $customer->code . " - " . $customer->name . " con el/los error/es: ";
                    foreach($customer->getErrorSummary(true) as $error) {
                        $message .= $error . "<br/>";
                    }
                    $messages[] = $message;
                    Yii::$app->session->set( '_batch_to_customer_messages_', $messages);
                    Yii::$app->session->close();
                }
            }

            return [
                'status' => 'success',
                'messages' => [],
                'total' => $total,
            ];
        }
    }

    /**
     * Filtro de customers para asignacion masiva de discount.
     *
     * @return string
     */
    public function actionDiscountToCustomer()
    {
        $searchModel = new CustomerContractSearch();

        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->search(Yii::$app->request->getQueryParams())
        ]);

        return $this->render('discount-to-customer', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    /**
     * Se asignan discount al customer
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDiscountToCustomerAssign()
    {
        set_time_limit(0);
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = 'json';

            $discount_id = (key_exists('discount_id', Yii::$app->request->post('CustomerContractSearch')) ? Yii::$app->request->post('CustomerContractSearch')['discount_id'] : 0 );
            $from_date = (key_exists('from_date', Yii::$app->request->post('CustomerContractSearch')) ? Yii::$app->request->post('CustomerContractSearch')['from_date'] : 0 );
            if($discount_id && $from_date) {
                $searchModel = new CustomerContractSearch();
                /** @var Query $query */
                $query = $searchModel->search(Yii::$app->request->getBodyParams());

                // Seteo la cantidad en la session
                $total = $query->count();
                Yii::$app->session->set( '_batch_to_customer_', [
                    'total' => $total,
                    'qty' => 0,
                    'withError' => 0
                ]);
                $messages = [];
                $i = 1;
                $withError = 0;
                $discount = Discount::findOne(['discount_id'=>$discount_id]);
                $to_date = (new \DateTime(Yii::$app->formatter->asDate($from_date)))->add(new \DateInterval("P".$discount->periods."M"));
                foreach ( $query->all() as $key => $value) {
                    $cusdis = new CustomerHasDiscount();
                    $cusdis->customer_id = $value['customer_id'];
                    $cusdis->discount_id = $discount_id;
                    $cusdis->from_date = $from_date;
                    $cusdis->status = Discount::STATUS_ENABLED;
                    $can = $cusdis->canAddDiscount();
                    if($can) {
                        $cusdis->save();
                    } else {
                        $messages[] = Yii::t('app', 'The customer "{customer}" is already assigned an active discount.', ['customer'=>$cusdis->customer->name]);
                        $withError++;
                    }

                    // Seteo la cantidad en la session
                    Yii::$app->session->set( '_batch_to_customer_', [
                        'total' => $total,
                        'qty' => $i,
                        'withError' => $withError
                    ]);
                    Yii::$app->session->close();
                    $i++;
                }

                return [
                    'status' => 'success',
                    'messages' => $messages,
                    'total' => $total,
                    'withError' => $withError
                ];
            } else {
                $errors = [];
                if(!$from_date) {
                    $errors['from_date'] = Yii::t('app', 'The date cant be empty.');
                }
                if(!$discount_id) {
                    $errors['discount_id'] = Yii::t('app', 'The discount cant be empty.');
                }
                return [
                    'status' => 'error',
                    'errors' => $errors
                ];
            }

        }
    }



    /**
     * Finds the IpRange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IpRange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contract::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Retorna el estado del proceso actual.
     * @return array
     */
    public function actionGetProcess()
    {
        Yii::$app->response->format = 'json';

        $process = Yii::$app->request->post('process');


        $messages = Yii::$app->session->get( '_batch_to_customer_messages_');
        Yii::$app->session->set( '_batch_to_customer_messages_', []);
        $return = Yii::$app->session->get($process, [
            'total' => 0,
            'qty'   => 0,
            'messages' => []
        ]);
        return array_merge($return, ['messages' => $messages]);
    }
}