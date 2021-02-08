<?php

namespace app\modules\sale\controllers;

use app\modules\sale\models\Customer;
use Yii;
use app\modules\sale\models\CustomerHasDiscount;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerHasDiscountController implements the CRUD actions for CustomerHasDiscount model.
 */
class CustomerHasDiscountController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all CustomerHasDiscount models.
     * @return mixed
     */
    public function actionIndex($customer_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CustomerHasDiscount::find()->where(['customer_id'=>$customer_id]),
        ]);

        $customer = Customer::findOne(['customer_id'=>$customer_id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'customer' => $customer
        ]);
    }

    /**
     * Displays a single CustomerHasDiscount model.
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
     * Creates a new CustomerHasDiscount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id)
    {
        $model = new CustomerHasDiscount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cutomer_has_discount_id]);
        } else {
            $model->customer_id = $customer_id;
            $customer = Customer::findOne($customer_id);
            return $this->render('create', [
                'model' => $model,
                'customer' => $customer
            ]);
        }
    }

    /**
     * Updates an existing CustomerHasDiscount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cutomer_has_discount_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CustomerHasDiscount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $customer_id = $model->customer_id;
        $model->delete();

        return $this->redirect(['index', 'customer_id' =>$customer_id]);
    }

    /**
     * Finds the CustomerHasDiscount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerHasDiscount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerHasDiscount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
