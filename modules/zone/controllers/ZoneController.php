<?php

namespace app\modules\zone\controllers;

use app\components\web\Controller;
use app\modules\zone\models\search\ZoneSearch;
use app\modules\zone\models\Zone;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ZoneController implements the CRUD actions for Zone model.
 */
class ZoneController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Zone models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ZoneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Zone model.
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
     * Creates a new Zone model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Zone();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->zone_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Zone model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->zone_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Zone model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionFullZone()
    {
        if(Yii::$app->request->isAjax){
            $zone_id=$_POST['zone_id'];
            $zone=$this->findModel($zone_id);
            $full_zone=$zone->getFullZone($zone_id);
            Yii::$app->response->format='json';
            $json=[];
            $json['status']='success';
            $json['fullzone']=$full_zone;
            return $json;
        }
        else{
            throw new NotFoundHttpException('The requested page does not exist.'); 
        }
    }
    
    public function actionZonesByName($name){
        if (Yii::$app->request->isAjax) {
            $zones= Zone::searchByName($name);
            $response= array();
            foreach ($zones as $zone) {
                $response['results'][]= ['id' => $zone->zone_id, 'text' => $zone->getFullZone($zone->zone_id)];
            }
            Yii::$app->response->format= Response::FORMAT_JSON;
            return $response;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.'); 
        }
    }

    /**
     * Finds the Zone model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Zone the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Zone::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
