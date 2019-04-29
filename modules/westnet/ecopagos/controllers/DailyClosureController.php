<?php

namespace app\modules\westnet\ecopagos\controllers;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\search\DailyClosureSearch;
use app\modules\westnet\ecopagos\models\DailyClosure;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * DailyClosureController implements the CRUD actions for DailyClosure model.
 */
class DailyClosureController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all DailyClosure models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new DailyClosureSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single DailyClosure model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);

        $allowed = true;

        if (UserHelper::isCashier()) {
            $payoutSearch = new \app\modules\westnet\ecopagos\models\search\PayoutSearch();
            $currentCashier = $payoutSearch->getCurrentCashier();
            if (!$model->canView($currentCashier->cashier_id)) {
                $allowed = false;
            }
        }

        if ($allowed) {
            return $this->render('view', [
                        'model' => $model,
            ]);
        } else {
            throw new \yii\web\ForbiddenHttpException('No tiene permisos para ver esta informacion');
        }
    }

    /**
     * Creates a new DailyClosure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new DailyClosure();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->daily_closure_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DailyClosure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->daily_closure_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DailyClosure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DailyClosure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DailyClosure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = DailyClosure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
