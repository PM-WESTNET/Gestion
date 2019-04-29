<?php

namespace app\modules\westnet\api\controllers;

use app\components\web\RestController;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use app\modules\zone\models\Zone;
use Yii;

class ZoneController extends RestController
{
    public $modelClass = 'app\modules\zone\models\Zone';

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['update'], $actions['index']);
        
        return $actions;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }

    /**
     * Retorna el contrato segun el id pasado como parametro.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $post = Yii::$app->request->post();

        $zone_id = (isset($post['id']) ?  $post['id'] : null);
        $name = (isset($post['name']) ?  $post['name'] : null);

        if($zone_id) {
            return Zone::findOne(['zone_id'=>$zone_id]);
        } else if($name){
            return Zone::find()->andWhere(['like', 'trim(lower(name))', strtolower($name)])->all();
        } else {
            return Zone::find()->all();
        }
    }
}