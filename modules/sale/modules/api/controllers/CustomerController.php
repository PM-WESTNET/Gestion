<?php

namespace app\modules\sale\modules\api\controllers;

use app\components\web\RestController;
use Yii;

class CustomerController extends RestController
{
    public $modelClass = 'app\modules\sale\models\Customer';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['index']);
        
        return $actions;
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new \app\modules\sale\models\search\CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $dataProvider->pagination = false;

        return $dataProvider;
    }
}
