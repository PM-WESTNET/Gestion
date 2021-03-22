<?php

namespace app\modules\instructive\controllers;

use app\components\web\Controller;
use app\modules\instructive\models\InstructiveCategory;
use phpDocumentor\Reflection\Types\Parent_;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\instructive\models\Instructive;
use app\modules\instructive\models\InstructiveSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InstructiveController implements the CRUD actions for Instructive model.
 */
class InstructiveController extends Controller
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
     * Lists all Instructive models.
     * @return mixed
     */
    public function actionIndex()
    {

        $roles = Role::getUserRoles(Yii::$app->user->id);
        $roles_array = [];

        foreach ($roles as $role) {
            $roles_array[] = $role->name;
        }

        $categories = InstructiveCategory::find();

        if (!User::hasRole('superadmin')) {
            $categories->leftJoin('instructive_category_has_role ichr', 'ichr.instructive_category_id=instructive_category.instructive_category_id')
                ->andWhere(['IN', 'ichr.role_code', $roles_array])
        ;
        }



        return $this->render('index', [
            'categories' => $categories->all()
        ]);
    }

    /**
     * Displays a single Instructive model.
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
     * Creates a new Instructive model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Instructive();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->instructive_id]);
        } else {

            $instructiveCategories = ArrayHelper::map(InstructiveCategory::find()->all(), 'instructive_category_id', 'name');
            return $this->render('create', [
                'model' => $model,
                'instructiveCategories' => $instructiveCategories,
            ]);
        }
    }

    /**
     * Updates an existing Instructive model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->instructive_id]);
        } else {

            $instructiveCategories = ArrayHelper::map(InstructiveCategory::find()->all(), 'instructive_category_id', 'name');

            return $this->render('update', [
                'model' => $model,
                'instructiveCategories' => $instructiveCategories,
            ]);
        }
    }

    /**
     * Deletes an existing Instructive model.
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
     * Finds the Instructive model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Instructive the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Instructive::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
