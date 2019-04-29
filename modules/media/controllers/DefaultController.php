<?php

namespace app\modules\media\controllers;

use Yii;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\media\models\MediaSearch;

class DefaultController extends Controller
{
    
    public $embedLayout = 'embed';
 
    public function behaviors()
    {
        
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
        
    }
    
    /**
     * Lists all Image models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
//    public function runAction($id, $params = []){
//        
//        $action = $this->createAction($id);
//        
//        if ($action === null && isset($params['class'])) {
//            
//            $class = new \ReflectionClass($params['class']);
//            $controller = strtolower($class->getShortName());
//            
//            $this->redirect([$controller."/$id", $params]);
//        }
//        
//        return parent::runAction($id, $params);
//        
//    }
    
    /**
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
