<?php

namespace app\modules\westnet\reports\controllers;

use app\modules\config\models\Config;
use app\modules\ticket\models\Category;
use app\modules\westnet\reports\search\CustomerSearch;
use Yii;
use app\components\web\Controller;

/**
 * CustomerController
 * Implementa los reportes relacionados con clientes.
 *
 */
class CustomerController extends Controller
{

    /**
     * List Customers per month
     *
     * @return mixed
     */
    public function actionCustomersPerMonth()
    {
        $search = new CustomerSearch();
        $data = $search->findPerMonthByDate((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());

        return $this->render('/customer/customer-per-month', [
            'model' => $search,
            'data'  => $data
            ]);
    }

    /**
     * Lista las razones de baja
     * @return string
     */
    public function actionReasonOfLow()
    {
        $search = new CustomerSearch();
        $data = $search->findByLowReason((!Yii::$app->request->isPost) ? null : Yii::$app->request->post());
        $category = Category::findOne(['category_id'=> Config::getValue('mesa_category_low_reason')]) ;
        $categories = Category::find()->where('lft >='. $category->lft . " and rgt <= " . $category->rgt)->all();

        return $this->render('/customer/reasons-of-low', [
            'model'         => $search,
            'data'          => $data,
            'categories'    => $categories
        ]);
    }
}
