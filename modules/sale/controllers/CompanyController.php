<?php

namespace app\modules\sale\controllers;

use Yii;
use app\modules\sale\models\Company;
use app\modules\sale\models\search\CompanySearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\web\Response;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '//fluid';
        
        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Company model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $dataProvider = new \yii\data\ActiveDataProvider();
        $dataProvider->query = $model->getPointsOfSale();
        
        return $this->render('view', [
            'model' => $model,
            'salePoints' => $dataProvider
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Company();

        if ($model->load(Yii::$app->request->post())&&$model->validate()) {
            $this->upload($model, 'certificate');
            $this->upload($model, 'key');
            $this->upload($model, 'logo');

            if($model->save()){
                return $this->redirect(['point-of-sale/create', 'company' => $model->company_id]);
            }
        } else {
            foreach( $model->getErrors() as $error) {
                Yii::$app->session->setFlash("error", Yii::t('app', $error[0]));
            }
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
        
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $certificate = $model->certificate;
        $key = $model->key;
        $logo = $model->logo;
        $certUpdate = Yii::$app->request->post('certificate_update', 0);
        $keyUpdate =  Yii::$app->request->post('key_update', 0);
        $logoUpdate =  Yii::$app->request->post('logo_update', 0);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($certUpdate) {
                $this->upload($model, 'certificate');
            } else {
                $model->certificate = $certificate;
            }
            if ($keyUpdate) {
                $this->upload($model, 'key');
            } else {
                $model->key = $key;
            }

            if ($logoUpdate) {
                $this->upload($model, 'logo');
            } else {
                $model->logo = $logo;
            }

            if($model->save()){
                return $this->redirect(['view', 'id' => $model->company_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
        
    }
    
    private function upload($model, $attr){
        
        $file = UploadedFile::getInstance($model, $attr);
        
        $folder = \yii\helpers\Inflector::pluralize($attr);

        if ($file && $model->validate()) {
            $filePath = Yii::$app->params['upload_directory'] . "$folder/". uniqid($attr) . '.' . $file->extension;
            
            if (!file_exists(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/")) {
                mkdir(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/", 0775, true);
            }
            
            $file->saveAs(Yii::getAlias('@webroot') . '/' . $filePath);
            
            $model->$attr = $filePath;

            return true;
        } else {
            return false;
        }
        
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Funcion que retorna los posibles nodos padre, dependiendo del servidor enviado
     */
    public function actionChildCompanies()
    {
        $out = [];
        $params = Yii::$app->request->post('depdrop_all_params');
        $company_id = ($params['parent_company_id'] ? $params['parent_company_id'] : null);
        if ($company_id) {
            $query = Company::find()
                ->select(['company_id as id', 'name as name'])
                ->where(['=', 'parent_id', $company_id]);
            $out = $query->asArray()->all();
            echo Json::encode(['output'=>$out, 'selected'=>'']);
        } else {
            echo Json::encode(['output'=>'', 'selected'=>'']);
        }
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
                    \Yii::trace('se conecta a api');
                    $valid_data = $api->validate(str_replace('-', '', $document));
                    $final_data = $api->extractData($valid_data);
                }
            }
        } catch (\Exception $ex) {
            $errors[] = [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ];
        }

        return [
            'status' => $valid_data ? true : false,
            'data' => $final_data
        ];
    }

    /**
     * @param $company_id
     * @return array
     * @throws NotFoundHttpException
     * Indica si la empresa o las empresas hijas tienen un canal de pago habilitado- canal de pago que use tarjetas de cobro
     */
    public function actionCompanyUsePaymentCard($company_id) {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($company_id);

        if(!$model) {
            return [
                'status' => 'error',
                'use_payment_card' => ''
            ];
        }

        return [
            'status' => 'success',
            'use_payment_card' => $model->hasEnabledTrackWithPaymentCards(true),
        ];

    }

}
