<?php

namespace app\modules\westnet\controllers;

use app\components\web\Controller;
use app\modules\westnet\models\search\NatServerSearch;
use app\modules\westnet\models\NatServer;
use yii\web\NotFoundHttpException;
use Yii;


/**
 * NodeController implements the CRUD actions for Node model.
 */
class NatServerController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Node models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new NatServerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    public function actionCreate(){
        $model = new NatServer();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = date('Y-m-d');
            $model->updated_at = date('Y-m-d');
            $model->save(false);

            return $this->redirect(['view', 'id' => $model->nat_server_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionView($id){
        $model = $this->findModel($id);
        $searchModel = new NatServerSearch();
        $dataProviderNodes = $searchModel->searchNodes($id);

        return $this->render('view', [
            'model' => $model,
            'dataProviderNodes' => $dataProviderNodes,
        ]);
    }

    public function actionUpdate($id){

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = date('Y-m-d');
            $model->save();
            return $this->redirect(['view', 'id' => $model->nat_server_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Finds the NatServer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Node the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NatServer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}