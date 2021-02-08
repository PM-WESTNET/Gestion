<?php

namespace app\modules\partner\controllers;

use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\partner\components\Movement;
use app\modules\partner\components\PartnerLiquidation;
use app\modules\partner\models\PartnerMovement;
use app\modules\partner\models\search\PartnerSearch;
use Yii;
use app\modules\partner\models\Partner;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PartnerController implements the CRUD actions for Partner model.
 */
class PartnerController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Partner models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->layout = '/fluid';
        $dataProvider = new ActiveDataProvider([
            'query' => Partner::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Partner model.
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
     * Creates a new Partner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Partner();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->partner_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Partner model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->partner_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Partner model.
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
     * Finds the Partner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Partner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Partner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * Muestra el detalle de cuenta del socio
     */
    public function actionAccount($id)
    {
        $model = $this->findModel($id);

        $query = (new PartnerSearch())->searchStatus($id);

        return $this->render('account', [
            'model' => $model,
            'status' => $query->all()
        ]);
    }

    public function actionShowAccountDetail($partner_id, $company_id )
    {

        $model = $this->findModel($partner_id);

        return $this->renderAjax('account-detail',[
            'model' => $model
        ]);
    }

    /**
     * Accion para el ingreso de dinero por parte de un socio
     * @param $id
     */
    public function actionInput($id)
    {
        $model = new PartnerMovement();
        $model->partner_id = $id;
        $model->input = true;
        if ($model->load(Yii::$app->request->post()) ) {
            $movement = new Movement();
            if($movement->input($model) ) {
                Yii::$app->session->setFlash("success", Yii::t('accounting', 'The movement has ben created succesfully.'));
                return $this->redirect(['account', 'id' => $model->partner_id]);
            } else {
                foreach ($movement->error as $msg) {
                    Yii::$app->session->setFlash("error", Yii::t('accounting', $msg));
                }
            }
        }
        return $this->render('movement', [
            'model' => $model,
        ]);
    }


    /**
     * Accion para el retiro de dinero de parte de un socio
     *
     * @param $id
     */
    public function actionWithdraw($id)
    {
        $model = new PartnerMovement();
        $model->partner_id = $id;
        $model->input = false;
        if ($model->load(Yii::$app->request->post()) ) {
            $movement = new Movement();
            if($movement->withDraw($model) ) {
                Yii::$app->session->setFlash("success", Yii::t('accounting', 'The movement has ben created succesfully.'));
                return $this->redirect(['account', 'id' => $model->partner_id]);
            } else {
                foreach ($movement->error as $msg) {
                    Yii::$app->session->setFlash("error", Yii::t('accounting', $msg));
                }
            }
        }
        return $this->render('movement', [
            'model' => $model,
        ]);
    }

    public function actionMovements($id)
    {
        set_time_limit(0);
        $model = $this->findModel($id);


        $searchModel = new AccountMovementSearch();
        $searchModel->account_id = $model->account_id;
        $dataProvider = $searchModel->searchForMovements(Yii::$app->request->get());

        return $this->render('partner-movements', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }
}
