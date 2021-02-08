<?php

namespace app\modules\westnet\reports\controllers;

use app\modules\westnet\reports\search\CompanyStatusSearch;
use app\modules\westnet\reports\search\CustomerSearch;
use Yii;
use app\components\web\Controller;

/**
 * CustomerController
 * Implementa los reportes relacionados con clientes.
 *
 */
class CompanyStatusController extends Controller
{

    /**
     * List Customers per month
     *
     * @return mixed
     */
    public function actionBillingChargedStatistics()
    {

        $search = new CompanyStatusSearch();
        $data = $search->findPerMonthByDate((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        return $this->render('/company-status/billing-charged-statistics', [
            'model' => $search,
            'data'  => $data
        ]);
    }

    /**
     * List Customers per month
     *
     * @return mixed
     */
    public function actionCostEffectiveness()
    {

        $search = new CompanyStatusSearch();
        $data = $search->costEffectiveness((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        return $this->render('/company-status/cost-effectiveness', [
            'model' => $search,
            'data'  => $data
        ]);
    }

    /**
     * List Customers per month
     *
     * @return mixed
     */
    public function actionDebtEvolution()
    {

        $search = new CompanyStatusSearch();
        $data = $search->debtEvolution((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        return $this->render('/company-status/debt-evolution', [
            'model' => $search,
            'data'  => $data
        ]);
    }
}
