<?php

namespace app\modules\instructive\controllers;

use app\components\web\Controller;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use Yii;
use app\modules\instructive\models\InstructiveCategory;
use app\modules\instructive\models\InstructiveCategorySearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InstructiveCategoryController implements the CRUD actions for InstructiveCategory model.
 */
class InstructiveCategoryController extends Controller
{
    public function behaviors()
    {
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
     * Lists all InstructiveCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InstructiveCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InstructiveCategory model.
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
     * Creates a new InstructiveCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InstructiveCategory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->instructive_category_id]);
        } else {
            $roles = ArrayHelper::map(Role::find()->all(), 'name', 'description');
            return $this->render('create', [
                'model' => $model,
                'roles' => $roles
            ]);
        }
    }

    /**
     * Updates an existing InstructiveCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->instructive_category_id]);
        } else {
            $roles = ArrayHelper::map(Role::find()->all(), 'name', 'description');
            return $this->render('update', [
                'model' => $model,
                'roles' => $roles
            ]);
        }
    }

    /**
     * Deletes an existing InstructiveCategory model.
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
     * Finds the InstructiveCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InstructiveCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InstructiveCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
