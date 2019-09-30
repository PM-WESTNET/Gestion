<?php

namespace app\modules\provider\controllers;

use app\modules\provider\models\Provider;
use app\modules\provider\models\ProviderBillHasTaxRate;
use app\modules\provider\models\ProviderBillItem;
use Yii;
use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\search\ProviderBillSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProviderBillController implements the CRUD actions for ProviderBill model.
 */
class ProviderBillController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all ProviderBill models.
     * @return mixed
     */
    public function actionIndex($provider_id=0)
    {
        $provider = null;
        $searchModel = new ProviderBillSearch();
        if (empty($searchModel->start_date)) {
            $searchModel->start_date=(new \DateTime('now -1 month'))->format('d-m-Y');
        }
        if ($provider_id!=0) {
            $searchModel->provider_id = $provider_id;
            $provider = $this->findProvider($provider_id);
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'provider'=>$provider
        ]);
    }

    /**
     * Displays a single ProviderBill model.
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
     * Creates a new ProviderBill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($provider=null, $from="index")
    {
        $model = new ProviderBill();
        if ($provider) {
            $provider = $this->findProvider($provider);
            $model->provider_id = $provider->provider_id;
            $model->type = $provider->bill_type;
        }

        if (empty($model->company_id)){
            $model->company_id = \app\modules\sale\models\Company::findDefault()->company_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $existing_provider_bill = ProviderBill::find()->where(['number' => $model->number1.'-'.$model->number2])->andWhere(['provider_id' => $model->provider_id])->one();
            if ($existing_provider_bill) {
                \Yii::$app->session->setFlash('error', 'Ya existe una factura con el mismo número para ese provedor');
                return $this->render('create', [
                            'model' => $model,
                            'dataProvider' => null,
                            'itemsDataProvider' => null,
                            'from' => $from
                ]);
            } elseif ($model->save()) {
                return $this->redirect(['provider-bill/update', 'id' => $model->provider_bill_id, 'from' => $from]);
            }
        } else {
            \app\components\helpers\FlashHelper::flashErrors($model);

            return $this->render('create', [
                        'model' => $model,
                        'dataProvider' => null,
                        'itemsDataProvider' => null,
                        'from' => $from
            ]);
        }
    }

    /**
     * Updates an existing ProviderBill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $from="index")
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $pay_after_save = Yii::$app->request->post('pay_after_save');
            
            if ($pay_after_save == 'true'){
                return $this->redirect(['/provider/provider-payment/create', 'provider' => $model->provider_id, 'from' => $from]);
            } else if ($from=="account") {
                return $this->redirect(['provider/current-account', 'id' => $model->provider_id, 'from' => $from]);
            } else {
                return $this->redirect(['view', 'id' => $model->provider_bill_id, 'from' => $from]);
            }
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $model->getProviderBillHasTaxRates(),
            ]);

            $itemsDataProvider = new ActiveDataProvider([
                'query' => $model->getProviderBillItems()
            ]);

            return $this->render('update', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'itemsDataProvider' => $itemsDataProvider,
                'from' => $from
            ]);
        }
    }

    /**
     * Deletes an existing ProviderBill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $from="index")
    {
        $model = $this->findModel($id);
        $provider_id = $model->provider_id;
        if($model->delete()) {
            Yii::$app->session->addFlash('success', Yii::t('app', 'The Invoice is successfully deleted.'));
        } else {
            Yii::$app->session->addFlash('error', Yii::t('app', 'The Invoice could not be deleted.'));
        }

        if ($from=="account") {

            return $this->redirect(['provider/account', 'id'=> $provider_id]);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the ProviderBill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProviderBill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProviderBill::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Provider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Provider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProvider($id)
    {
        if (($model = \app\modules\provider\models\Provider::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionAddTax($id)
    {
        $model = $this->findModel($id);
        $tax = new ProviderBillHasTaxRate();
        $tax->load(Yii::$app->request->post());

        if($tax->validate()){
            $tax = $model->addTax([
                'provider_bill_id'  => $tax->provider_bill_id,
                'tax_rate_id'       => $tax->tax_rate_id,
                'amount'            => $tax->amount
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillHasTaxRates(),
        ]);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillItems(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'itemsDataProvider' => $itemsDataProvider,
            'from' => 'index'
        ]);
    }

    /**
     * Deletes an existing AccountConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteTax($provider_bill_id, $tax_rate_id)
    {
        $modelDelete = ProviderBillHasTaxRate::findOne([
            'provider_bill_id'=> $provider_bill_id,
            'tax_rate_id'=> $tax_rate_id]);
        if(!empty($modelDelete)) {
            $modelDelete->delete();
        }

        $model = $this->findModel($provider_bill_id);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillItems(),
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillHasTaxRates(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'from' => 'index',
            'itemsDataProvider' => $itemsDataProvider,
        ]);

    }

    /**
     *
     * @param int $id
     * @return json
     */
    public function actionAddItem($id)
    {
        $model = $this->findModel($id);
        $item = new ProviderBillItem();
        $item->load(Yii::$app->request->post());

        if($item->validate()){
            $item = $model->addItem([
                'provider_bill_id'  => $item->provider_bill_id,
                'account_id'        => $item->account_id,
                'description'       => $item->description,
                'amount'            => $item->amount
            ]);

            $model->calculateTotal();
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillHasTaxRates(),
        ]);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillItems(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'itemsDataProvider' => $itemsDataProvider,
            'from' => 'index'
        ]);
    }

    /**
     * Deletes an existing AccountConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($provider_bill_id, $provider_bill_item_id)
    {
        $modelDelete = ProviderBillItem::findOne([
            'provider_bill_id'=> $provider_bill_id,
            'provider_bill_item_id'=> $provider_bill_item_id]);
        if(!empty($modelDelete)) {
            $modelDelete->delete();
        }

        $model = $this->findModel($provider_bill_id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillHasTaxRates(),
        ]);

        $itemsDataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillItems(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'itemsDataProvider' => $itemsDataProvider,
            'from' => 'index'
        ]);

    }

    public function actionListItems($provider_bill_id)
    {
        $this->layout = '//empty';
        $model = $this->findModel($provider_bill_id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getProviderBillItems(),
        ]);

        return $this->render('_items', [
            'dataProvider' => $dataProvider
        ]);
    }
}
