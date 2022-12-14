<?php

namespace app\modules\ticket\controllers;

use app\modules\config\models\Config;
use app\modules\ticket\models\search\CategorySearch;
use app\modules\westnet\mesa\components\request\UsuarioRequest;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\ticket\models\Category;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find(),
        ]);

        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updateTree();
            return $this->redirect(['view', 'id' => $model->category_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updateTree();
            return $this->redirect(['view', 'id' => $model->category_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
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
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetExternalUsers()
    {
        if (YII_ENV_TEST) {
            return null;
        }

        Yii::$app->response->format = 'json';

        $api = new UsuarioRequest(Config::getValue('mesa_server_address'));
        $response = $api->findAll();

        return $response;
    }

    /**
     * @param $category_id
     * @return array
     * Devuelve el usuario de gestion que esta asignado como responsable de una categor??a
     */
    public function actionGetResponsibleUserByCategory($category_id)
    {
        Yii::$app->response->format = 'json';
        $category = Category::findOne($category_id);
        if($category->responsible_user_id) {
            return [
                'item' => [
                    'id' => $category->responsibleUser->id,
                    'value' => $category->responsibleUser->username
                ],
                'status' => 'success',
            ];
        }

        return [
            'item' => [],
            'status' => 'error',
        ];
    }

    /**
     * @throws NotFoundHttpException
     * Devuelve un json con 'id' => id de estado, 'name' =>  nombre de estado
     */
    public function actionGetStatusFromSchema()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null && $parents[0]) {
                $category = $this->findModel($parents[0]);
                $out = $category->schema->getStatusesBySchema();
                echo Json::encode(['output' => $out, 'selected' => '']);
                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }
}
