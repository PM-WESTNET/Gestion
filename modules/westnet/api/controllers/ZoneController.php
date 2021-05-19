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
            'index' => ['GET', 'HEAD', 'POST'],
        ];
    }


    /**
     * @SWG\Post(path="/isp/api/zone/index",
     *     tags={"Zona"},
     *     summary="",
     *     description="Retorna la zona segun el id pasado como parametro.",
     *     produces={"application/json"},
     *     security={{"auth":{}}},
     *     @SWG\Parameter(
     *        in = "body",
     *        name = "body",
     *        description = "",
     *        required = true,
     *        type = "integer",
     *        @SWG\Schema(
     *          @SWG\Property(property="zone_id", type="integer", description="ID de la zona"),
     *        )
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "
     *           {
     *               
     *           }
     *                      
     *         "
     *
     *     ),
     *       @SWG\Response(
     *         response = 400,
     *         description = "
     *            {
     *               'Error' => true,
     *                'Message' => 'No zone_id specified.'
     *             }
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */

    /**
     * Retorna la zona segun el id pasado como parametro.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $post = Yii::$app->request->post();

        $zone_id = (isset($post['zone_id']) ?  $post['zone_id'] : null);
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