<?php

namespace app\modules\westnet\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\IpRange;
use app\modules\westnet\models\AccessPoint;
use app\modules\westnet\models\search\AccessPointSearch;

/**
 * AccessPointController implements the CRUD actions for AccessPoint model.
 */
class AccessPointController extends Controller
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
     * Lists all AccessPoint models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccessPointSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccessPoint model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AccessPoint model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AccessPoint();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('app', 'Access Point created succesfully'));
            return $this->redirect(['assign-ip-range', 'ap_id' => $model->access_point_id]);
        }

        $nodes = ArrayHelper::map(Node::find()->andWhere(['status' => 'enabled'])->all(), 'node_id', 'name');

        return $this->render('create', [
            'model' => $model,
            'nodes' => $nodes
        ]);
    }

    /**
     * Updates an existing AccessPoint model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->access_point_id]);
        }

        $nodes = ArrayHelper::map(Node::find()->andWhere(['status' => 'enabled'])->all(), 'node_id', 'name');

        return $this->render('update', [
            'model' => $model,
            'nodes' => $nodes
        ]);
    }

    /**
     * Deletes an existing AccessPoint model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AccessPoint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccessPoint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccessPoint::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionAssignIpRange($ap_id) {
        $model = $this->findModel($ap_id);

        if ($model->load(Yii::$app->request->post()) && $model->assignIpRange()) {
            return $this->redirect(['view', 'id' => $model->access_point_id]);
        }

        $rangeQuery = IpRange::find()
            ->andWhere(['status' => 'enabled'])
            ->andWhere(['type' => IpRange::SUBNET_TYPE])
            ->andWhere(['IS', 'ap_id', null])
            ->orderBy(['ip_start' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider(['query' => $rangeQuery]);

        return $this->render('assign-range', ['dataProvider' => $dataProvider, 'model' => $model]);

    }
}
