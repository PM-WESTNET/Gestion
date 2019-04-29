<?php

namespace app\modules\config\controllers;

use Yii;
use app\modules\config\models\Rule;
use app\modules\config\models\search\RuleSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RuleController implements the CRUD actions for Rule model.
 */
class RuleController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'load-attributes' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rule model.
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
     * Creates a new Rule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($item)
    {
        $model = new Rule();
        Rule::getValidatorAttributes('integer');
        
        $item = \app\modules\config\models\Item::findOne($item);
        if($item === null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $model->item_id = $item->item_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['item/view', 'id' => $model->item_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'item' => $item
            ]);
        }
    }
    
    public function actionLoadAttributes()
    {
        
        $validator = Yii::$app->request->post('validator');
        
        $attrs = Rule::getValidatorAttributes($validator);
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return [
            'attributes' => $attrs,
            'status' => 'success'
        ];
        
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['item/view', 'id' => $model->item_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Rule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect( Yii::$app->request->referrer );
    }

    /**
     * Finds the Rule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
