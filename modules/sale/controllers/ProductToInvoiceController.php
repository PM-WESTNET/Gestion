<?php

namespace app\modules\sale\controllers;

use app\modules\sale\models\Customer;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\models\search\ProductToInvoiceSearch;
use Yii;
use app\modules\sale\modules\contract\models\Plan;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * PlanController implements the CRUD actions for Plan model.
 */
class ProductToInvoiceController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Plan models.
     * @return mixed
     */
    public function actionIndex($customer_id)
    {

        $customer = Customer::findOne($customer_id);

        $searchModel = new ProductToInvoiceSearch();
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->searchByCustomer($customer_id)
        ]);

        return $this->render('index', [
            'customer' => $customer,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Plan model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $customer_id)
    {

        $customer = Customer::findOne($customer_id);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'customer' => $customer,
        ]);
    }

    /**
     * Updates an existing Plan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $customer_id)
    {
        $model = $this->findModel($id);
        $customer = Customer::findOne($customer_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->product_to_invoice_id, 'customer_id' => $customer_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'customer' => $customer
            ]);
        }
    }
    
    /**
     * Cancel an existing Product to invoice
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCancel($id, $customer_id)
    {
        $model = $this->findModel($id);

        $model->changeState(ProductToInvoice::STATUS_CANCELED);

        return $this->redirect(['index', 'customer_id'=> $customer_id]);
    }

    /**
     * Activate an existing Product to invoice
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionActivate($id, $customer_id)
    {
        $model = $this->findModel($id);

        $model->changeState(ProductToInvoice::STATUS_ACTIVE);

        return $this->redirect(['index', 'customer_id'=> $customer_id]);
    }


    /**
     * Finds the Plan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Plan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductToInvoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
