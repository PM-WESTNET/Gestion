<?php

namespace app\modules\westnet\controllers;

use app\modules\westnet\models\search\ConnectionSearch;
use app\modules\westnet\models\search\ServerSearch;
use Yii;
use app\modules\westnet\models\Server;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ServerController implements the CRUD actions for Server model.
 */
class ServerController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Server models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Server::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Server model.
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
     * Creates a new Server model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Server();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->server_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Server model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->server_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Server model.
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
     * Finds the Server model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Server the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Server::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     */
    public function actionMoveCustomers($id)
    {
        set_time_limit(0);
        $model = $this->findModel($id);
        $search = new ConnectionSearch();
        $qty = $search->findByServer($id)->count();
        if(!Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            $errors = ['destination_server_id'=>Yii::t('westnet', 'Destination server not selected.')];
            $destination_server_id = Yii::$app->request->post('destination_server_id');
            if ($destination_server_id) {
                $errors = $model->moveCustomersTo($destination_server_id);
            }
            return [
                'qty'    => $qty,
                'errors' => $errors,
            ];
        }

        return $this->render('move-customers', [
            'model' => $model,
            'qty'   => $qty
        ]);
    }


    /**
     *
     */
    public function actionRestoreCustomers($id)
    {
        $model = $this->findModel($id);
        $search = new ConnectionSearch();
        $qty = $search->findByServerToRestore($id)->count();
        if(!Yii::$app->request->isGet && Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            $errors = $model->restoreCustomersFromNode();

            return [
                'qty'    => $qty,
                'errors' => $errors,
            ];
        }

        return $this->render('restore-customers', [
            'model' => $model,
            'qty'   => $qty
        ]);
    }
}