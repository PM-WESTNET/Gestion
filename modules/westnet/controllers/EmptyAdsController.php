<?php

namespace app\modules\westnet\controllers;

use app\modules\westnet\models\EmptyAds;
use app\modules\westnet\models\search\EmptyAdsSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * EmptyAdsController implements the CRUD actions for EmptyAds model.
 */
class EmptyAdsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EmptyAds models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $dataProvider = new ActiveDataProvider(['query' => EmptyAds::find()->where(['used' => false])]);

        return $this->render('index', [
            
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionSearchAds($code){
        if (Yii::$app->request->isAjax) {
                    
            $searchAds = new EmptyAdsSearch();

            Yii::$app->response->format = Response::FORMAT_JSON;

            $response = $searchAds->searchForAutocomplete($code);

            return $response;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.'); 
        }
    }

    /**
     * Finds the EmptyAds model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmptyAds the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmptyAds::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
