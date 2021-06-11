<?php

namespace app\modules\firstdata\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\firstdata\models\FirstdataAutomaticDebit;
use app\modules\firstdata\models\search\FirstdataAutomaticDebitSearch;
use app\modules\config\models\Config;
/**
 * FirstdataAutomaticDebitController implements the CRUD actions for FirstdataAutomaticDebit model.
 */
class FirstdataAutomaticDebitController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all FirstdataAutomaticDebit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FirstdataAutomaticDebitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FirstdataAutomaticDebit model.
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
     * Creates a new FirstdataAutomaticDebit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FirstdataAutomaticDebit();
        $roles_for_adherence = explode(',',Config::getConfig('roles_for_adherence')->getDescription());
        
        foreach ($roles_for_adherence as $key => $value) {
            unset($roles_for_adherence[$key]);
            $roles_for_adherence[$value] = $value;
        }

        $model->scenario = 'insert';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->firstdata_automatic_debit_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'roles_for_adherence' => $roles_for_adherence,
        ]);
    }

    /**
     * Updates an existing FirstdataAutomaticDebit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $roles_for_adherence = explode(',',Config::getConfig('roles_for_adherence')->getDescription());
        
        foreach ($roles_for_adherence as $key => $value) {
            unset($roles_for_adherence[$key]);
            $roles_for_adherence[$value] = $value;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {   
            return $this->redirect(['view', 'id' => $model->firstdata_automatic_debit_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'roles_for_adherence' => $roles_for_adherence,
        ]);
    }

    /**
     * Deletes an existing FirstdataAutomaticDebit model.
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
     * Finds the FirstdataAutomaticDebit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FirstdataAutomaticDebit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FirstdataAutomaticDebit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
