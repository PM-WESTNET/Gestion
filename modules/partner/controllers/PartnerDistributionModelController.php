<?php

namespace app\modules\partner\controllers;

use app\modules\partner\models\PartnerDistributionModelHasPartner;
use Yii;
use app\modules\partner\models\PartnerDistributionModel;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PartnerDistributionModelController implements the CRUD actions for PartnerDistributionModel model.
 */
class PartnerDistributionModelController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all PartnerDistributionModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PartnerDistributionModel::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PartnerDistributionModel model.
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
     * Creates a new PartnerDistributionModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PartnerDistributionModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->partner_distribution_model_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PartnerDistributionModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->partner_distribution_model_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PartnerDistributionModel model.
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
     * Finds the PartnerDistributionModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PartnerDistributionModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PartnerDistributionModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Agrega un item movimiento
     * @param $id
     */
    public function actionAddPartner($partner_distribution_model_id=null, $partner_distribution_model_has_partner_id=null)
    {
        $model = $this->findModel($partner_distribution_model_id);

        $item = new PartnerDistributionModelHasPartner();
        $id = $partner_distribution_model_has_partner_id ? $partner_distribution_model_has_partner_id : Yii::$app->request->post('PartnerDistributionModelHasPartner')['partner_distribution_model_has_partner_id'];
        if($id) {
            $item = PartnerDistributionModelHasPartner::findOne(['partner_distribution_model_has_partner_id'=>$id]);
        }
        if ($item->load(Yii::$app->request->post()) && $item->validate()) {
            if($id) {
                $item->partner_distribution_model_has_partner_id = $id;
                $item->updateAttributes(['percentage']);
            } else {
                $item->save();
            }
            $item = new PartnerDistributionModelHasPartner();
        }

        return $this->renderAjax('_form-partner', [
            'model' => $model,
            'item'  => $item
        ]);
    }

    /**
     * Se listan los items del movimiento.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionListPartner($partner_distribution_model_id)
    {
        $model = $this->findModel($partner_distribution_model_id);

        $items = new ActiveDataProvider([
            'query' => $model->getPartnerDistributionModelHasPartner()
        ]);

        return $this->renderAjax('_form-list-partner', [
            'model' => $model,
            'items' => $items
        ]);
    }


    /**
     * Borra un item movimiento
     *
     * @param $id
     * @param $account_movement_item_id
     */
    public function actionDeletePartner($partner_distribution_model_has_partner_id)
    {
        $item = PartnerDistributionModelHasPartner::findOne( $partner_distribution_model_has_partner_id );
        $partner_distribution_model_id = $item->partner_distribution_model_id;
        if($item) {
            $item->delete();
        }
        return $this->actionListPartner($partner_distribution_model_id);
    }

    public function actionGetByCompany($company_id)
    {
        Yii::$app->response->format = 'json';

        $result = PartnerDistributionModel::findAll(['company_id'=> $company_id]);
        return $result;
    }
}
