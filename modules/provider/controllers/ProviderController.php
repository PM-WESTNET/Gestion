<?php

namespace app\modules\provider\controllers;

use Yii;
use app\modules\provider\models\Provider;
use app\modules\provider\models\search\ProviderSearch;
use app\modules\provider\models\search\ProviderBillSearch;
use app\modules\provider\models\search\ProviderPaymentSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\afip\components\CuitOnlineValidator;
use app\modules\sale\models\Company;
use app\modules\invoice\components\einvoice\ApiFactory;

/**
 * ProviderController implements the CRUD actions for Provider model.
 */
class ProviderController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all Provider models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';

        $searchModel = new ProviderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Provider model.
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
     * Creates a new Provider model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Provider();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->provider_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Provider model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->provider_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Provider model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCurrentAccount($id)
    {

        $this->layout = '/fluid';
        $model = $this->findModel($id);

        // Para los totales
        $providerSearch = new ProviderSearch();
        $providerSearch->provider_id = $id;

        // Payments
        $paymentSearchModel = new ProviderPaymentSearch();
        $paymentDataProvider = $paymentSearchModel->searchAccount($id, Yii::$app->request->queryParams);


        return $this->render('account', [
            'model' => $model,
            'searchModel' => $paymentSearchModel,
            'dataProvider' => $paymentDataProvider,
            'providerSearch' => $providerSearch
        ]);

    }

    /**
     * Finds the Provider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Provider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Provider::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Muestra un listado con las deudas a los proveedores.
     *
     * @return mixed
     */
    public function actionDebts()
    {
        $searchModel = new ProviderSearch;
        $dataProvider = $searchModel->searchDebts(Yii::$app->request->getQueryParams());

        return $this->render('debts', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Busca provider por nombre
     * @param $name
     * @return array
     */
    public function actionFindByName($name, $id = null)
    {
        Yii::$app->response->format = 'json';

        if (!is_null($name)) {
            $searchModel = new ProviderSearch();
            $data['results'] = $searchModel->searchBy($name)
                ->select(['provider_id as id', 'name as text'])
                ->asArray()->all();
        } else if ($id > 0) {
            $data['results'] = ['id' => $id, 'text' => Provider::find($id)->name];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function actionBillsAndPayments()
    {
        $searchModel = new ProviderSearch;
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->findBillsAndPayments(Yii::$app->request->getQueryParams()),
        ]);
        $dataProvider->setSort(['attributes' => [
            'name',
            'facturado',
            'pagos'
        ]]);
        $totals = $searchModel->findBillsAndPaymentsTotals(Yii::$app->request->getQueryParams());


        return $this->render('bills-and-payments', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'totals' => $totals
        ]);
    }

    public function actionGetBillTypeByProvider()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $provider_id = Yii::$app->request->post('provider_id');
        $provider = Provider::findOne($provider_id);
        $tax_condition = \app\modules\sale\models\TaxCondition::findOne($provider->tax_condition_id);
        $bill_types = $tax_condition->billTypesBuy;
        $data = [];
        foreach ($bill_types as $bill_type) {
            $data[] = [
                'id' => $bill_type->bill_type_id,
                'text' => $bill_type->name
            ];
        }

        return $data;
    }

    public function actionAfipValidation($document)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Obtengo la session para ver si tengo guardado algun token
        $session = Yii::$app->session;

        $params = Yii::$app->params['afip-validation'];

        /** @var CuitOnlineValidator $api */
        $api = ApiFactory::getInstance()->getApi(CuitOnlineValidator::class);
        $company = Company::findOne(['company_id' => $params['company_id']]);

        $valid_data = '';
        $final_data = '';
        $errors = [];

        $api->setCompany($company);
        $api->setTesting($params['testing']);
        $api->setUseOnline($params['use-online']);
        $api->setSaveCalls($params['save-calls']);
        if ($session->has("afip_token")) {
            $api->setTokens($session->get("afip_token"));
        }
        try {
            if (!$api->isTokenValid()) {
                $certificate = Yii::getAlias('@webroot') . '/' . $company->certificate;
                $key = Yii::getAlias('@webroot') . '/' . $company->key;
                $authorize = $api->authorize($certificate, $key, $company->certificate_phrase);
                $session->set("afip_token", $api->getTokens());
            }
            if ($api->isTokenValid() || $authorize) {
                error_log("4");

                if ($api->connect([], ["ssl" => ["ciphers" => "TLSv1"]], 'SOAP_1_1')) {
                    error_log("5");
                    \Yii::debug('se conecta a api');
                    $valid_data = $api->validate(str_replace('-', '', $document));
                    $final_data = $api->extractData($valid_data);
                }
            }
        } catch (\Exception $ex) {
            \Yii::debug($ex);
            $errors[] = [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ];
        }

        return [
            'status' => $valid_data ? true : false,
            'data' => $final_data,
            'errors' => $errors
        ];
    }

}
