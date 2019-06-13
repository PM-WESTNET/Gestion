<?php

namespace app\modules\westnet\controllers;

use app\modules\sale\models\Company;
use Yii;
use app\modules\westnet\models\AdsPercentagePerCompany;
use app\modules\westnet\models\search\AdsPercentagePerCompanySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AdsPercentagePerCompanyController implements the CRUD actions for AdsPercentagePerCompany model.
 */
class AdsPercentagePerCompanyController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AdsPercentagePerCompany models.
     * @return mixed
     */
    public function actionIndex()
    {
        $bad_configuration = AdsPercentagePerCompany::getVerifyParentCompaniesConfigADSPercentageAsString();
        if($bad_configuration) {
            Yii::$app->session->setFlash('error', $bad_configuration);
        }

        $child_companies = Company::find()
            ->leftJoin('ads_percentage_per_company appc', 'appc.company_id = company.company_id and appc.parent_company_id = company.parent_id')
            ->where(['not',['company.parent_id' => null]])
            ->orderBy(['parent_id' => SORT_ASC])->all();

        $parent_companies = Company::find()->where(['parent_id' => null])->all();

        return $this->render('index', [
            'parent_companies' => $parent_companies,
            'companies' => $child_companies
        ]);
    }

    /**
     * @param $parent_company_id
     * @param $company_id
     * @return array
     * Actualiza el valor del porcentaje de una empresa
     */
    public function actionUpdateCompanyPercentage($company_id)
    {
        if (isset($_POST['hasEditable'])) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            if (AdsPercentagePerCompany::setCompanyPercentage($company_id, Yii::$app->request->post('percentage'))){
                return ['output' => Yii::$app->request->post('percentage'), 'message' => ''];
            } else {
                return ['output' => 'false', 'message' => Yii::t('app', 'An error occurred')];
            }
        }
    }

    /**
     * Finds the AdsPercentagePerCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdsPercentagePerCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdsPercentagePerCompany::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
