<?php

namespace app\modules\agenda\controllers;

use Yii;
use app\modules\agenda\models\UserGroup;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserGroupController implements the CRUD actions for UserGroup model.
 */
class UserGroupController extends Controller 
{

    /**
     * Lists all UserGroup models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => UserGroup::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new UserGroup();

        if (!empty($_POST['Task']['users']))
            $model->users = $_POST['Task']['users'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->group_id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if (!empty($_POST['Task']['users']))
            $model->users = $_POST['Task']['users'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->group_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * @brief Devuelve un usuario segun el username que venga por post
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetUserByUsername() {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $json = [];
        
        if (isset(Yii::$app->modules['agenda']->params['user']['class']))
            $userModelClass = Yii::$app->modules['agenda']->params['user']['class'];
        else
            $userModelClass = 'User';
        if (isset(Yii::$app->modules['agenda']->params['user']['idAttribute']))
            $userModelId = Yii::$app->modules['agenda']->params['user']['idAttribute'];
        else
            $userModelId = 'id';

        if ($post = Yii::$app->request->post()) {

            $username = $post['username'];

            $user = $userModelClass::find()->where([
                'username' => $username
            ])->one();

            if (!empty($user)) {
                $json['status'] = "success";
                $json['user'] = $user;
                $json['message'] = "Usuario encontrado.";
            } else {
                $json['status'] = "error";
                $json['message'] = "Error";
            }

            return $json;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the UserGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = UserGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
