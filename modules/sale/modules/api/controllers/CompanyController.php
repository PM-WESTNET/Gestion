<?php

namespace app\modules\sale\modules\api\controllers;

use app\components\web\RestController;

class CompanyController extends RestController
{
    public $modelClass = 'app\modules\sale\models\Company';
    
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update']);
        
        return $actions;
    }
    
    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $searchModel = new \app\modules\sale\models\search\CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = false;
        
        return $dataProvider;
    }
}
