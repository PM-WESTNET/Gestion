<?php

namespace app\modules\sale\controllers;

use Yii;
use app\modules\sale\models\Observation;
use app\modules\sale\models\ObservationSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Response;

/**
 * ObservationController implements the CRUD actions for Observations model.
 */
class ObservationController extends Controller
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
     * Lists all Observations models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ObservationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Observations model.
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
     * Creates a new Observations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateObservation($customer_id)
    {
        $request = Yii::$app->request;

        if ($request->isAjax) {

            $model = new Observation();
            $model->observation = $request->get('observation');
            $model->customer_id = $request->get('customer_id');
            $model->author_id = Yii::$app->user->identity->id;
            $model->date = date('Y-m-d h:i:s');           
            $result = $model->save();
            if($result){
                Yii::$app->response->statusCode = 200;
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                return ['message' => 'Observación guardada con éxito.', 'result' => $result];
            }else{
                Yii::$app->response->statusCode = 400;
                Yii::$app->response->format = Response::FORMAT_JSON;
                $err= $model->getErrors();
                return ['message' => 'No se pudo guardar la observación.', 'result' => $result, 'errors' => $err ];
            }


            // return $response;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }

    /**
     * Updates an existing Observations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Observations model.
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
     * Finds the Observations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Observation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Observation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function getObservationDataProvider($c){
        
        $query = New Query();
        $dataProvider = new ActiveDataProvider([
            'query' => Observation::find(["customer_id" => $c->customer_id]),
            'pagination' => [
                'pageSize'=> 5,
            ],
            'sort'=> ['defaultOrder' => ['date' => SORT_DESC]],
        ]);

        // var_dump($dataProvider->getModels());die();

        return $dataProvider;
    }

    public function getAuthor($m){
        
        $query = New Query();
        $result = $query->from("user")->where("id = ". $m->author_id);
            

        return $result;
    }
}
