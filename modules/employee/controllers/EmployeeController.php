<?php

namespace app\modules\employee\controllers;

use app\modules\employee\models\EmployeeCategory;
use app\modules\sale\models\Address;
use Yii;
use app\modules\employee\models\Employee;
use app\modules\employee\models\search\EmployeeSearch;
use app\modules\employee\models\search\EmployeeBillSearch;
use app\modules\employee\models\search\EmployeePaymentSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\afip\components\CuitOnlineValidator;
use app\modules\sale\models\Company;
use app\modules\invoice\components\einvoice\ApiFactory;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';

        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
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
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();
        $address = new Address();

        if ($model->load(Yii::$app->request->post()) && $address->load(Yii::$app->request->post())) {
            if ($address->save()) {
                $model->address_id = $address->address_id;
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->employee_id]);
                }
            }
        }
        Yii::trace($model->getErrors());

        $categories = ArrayHelper::map(EmployeeCategory::find()->andWhere(['status' => 'enabled'])->all(), 'employee_category_id', 'name');

        return $this->render('create', [
            'model' => $model,
            'address' => $address,
            'categories' => $categories
        ]);

    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $address = $model->address;

        if ($model->load(Yii::$app->request->post()) && $address->load(Yii::$app->request->post())
            && $address->save() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->employee_id]);
        }

        $categories = ArrayHelper::map(EmployeeCategory::find()->andWhere(['status' => 'enabled'])->all(), 'employee_category_id', 'name');


        return $this->render('update', [
            'model' => $model,
            'address' => $address,
            'categories' => $categories
        ]);

    }

    /**
     * Deletes an existing Employee model.
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
        $employeeSearch = new EmployeeSearch();
        $employeeSearch->employee_id = $id;

        // Payments
        $paymentSearchModel = new EmployeePaymentSearch();
        $paymentDataProvider = $paymentSearchModel->searchAccount($id, Yii::$app->request->queryParams);


        return $this->render('account', [
            'model' => $model,
            'searchModel' => $paymentSearchModel,
            'dataProvider' => $paymentDataProvider,
            'employeeSearch' => $employeeSearch
        ]);

    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
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
        $searchModel = new EmployeeSearch;
        $dataProvider = $searchModel->searchDebts(Yii::$app->request->getQueryParams());

        return $this->render('debts', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Busca employee por nombre
     * @param $name
     * @return array
     */
    public function actionFindByName($name, $id = null)
    {
        Yii::$app->response->format = 'json';

        if (!is_null($name)) {
            $searchModel = new EmployeeSearch();
            $data['results'] = $searchModel->searchBy($name)
                ->select(['employee_id as id', 'name as text'])
                ->asArray()->all();
        } else if ($id > 0) {
            $data['results'] = ['id' => $id, 'text' => Employee::find($id)->name];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function actionBillsAndPayments()
    {
        $searchModel = new EmployeeSearch;
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

    public function actionGetBillTypeByEmployee()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $employee_id = Yii::$app->request->post('employee_id');
        $employee = Employee::findOne($employee_id);
        $tax_condition = \app\modules\sale\models\TaxCondition::findOne($employee->tax_condition_id);
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
