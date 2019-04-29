<?php

namespace app\modules\sale\modules\contract\controllers;

use Yii;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\search\ContractDetailSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ContractDetailController implements the CRUD actions for ContractDetail model.
 */
class ContractDetailController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all ContractDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContractDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ContractDetail model.
     * @param integer $contract_id
     * @param integer $product_id
     * @return mixed
     */
    public function actionView($contract_id, $product_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($contract_id, $product_id),
        ]);
    }

    /**
     * Creates a new ContractDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContractDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'contract_id' => $model->contract_id, 'product_id' => $model->product_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ContractDetail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $contract_id
     * @param integer $product_id
     * @return mixed
     */
    public function actionUpdate($contract_id, $product_id)
    {
        $model = $this->findModel($contract_id, $product_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'contract_id' => $model->contract_id, 'product_id' => $model->product_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContractDetail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $contract_id
     * @param integer $product_id
     * @return mixed
     */
    public function actionDelete($contract_id, $product_id)
    {
        $this->findModel($contract_id, $product_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContractDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $contract_id
     * @param integer $product_id
     * @return ContractDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($contract_id, $product_id)
    {
        if (($model = ContractDetail::findOne(['contract_id' => $contract_id, 'product_id' => $product_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
