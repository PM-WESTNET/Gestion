<?php

namespace app\modules\sale\modules\api\controllers;

use app\components\web\RestController;
use Yii;

class CategoryController extends RestController
{
    public $modelClass = 'app\modules\sale\models\Category';
    
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['index'], $actions['create']);
        
        return $actions;
    }
    
    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $models = \app\modules\sale\models\Category::find()->where(['parent_id' => null, 'status' => 'enabled'])->all();
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'models' => $models,
            'pagination' => false
        ]);
        
        return $dataProvider;
        
    }
}
