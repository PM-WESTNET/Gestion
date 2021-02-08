<?php

namespace app\modules\sale\modules\api\controllers;

use app\components\web\RestController;
use Yii;

class ProductController extends RestController
{
    public $modelClass = 'app\modules\sale\models\Product';
    
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['create'], $actions['index']);

        return $actions;
    }
    
    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $searchModel = new \app\modules\sale\models\search\ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = false;
        
        return $dataProvider;
    }
}
