<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 16/07/19
 * Time: 13:14
 */

namespace app\modules\ivr\v1\controllers;


use app\modules\ivr\v1\components\Controller;
use app\modules\ivr\v1\models\Customer;
use app\modules\ivr\v1\models\search\CustomerSearch;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class CustomerController extends Controller
{


    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'search' => ['POST']
                ],
            ],
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * @SWG\Post(path="/customer/search",
     *     tags={"Customer"},
     *     summary="",
     *     description="Devuelve un array con los clientes encontrados segun el criterio usado",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "field",
     *        description = "Campo por el cual buscar al cliente. Puede ser 'document_number' para el documento y 'code' para el código de cliente'",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "value",
     *        description = "El valor para el campo a buscar",
     *        required = true,
     *        type = "string"
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "Devuelve un array con el/los clientes activos encontrados"
     *
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Parametro faltante, cliente no encontrado, o error de autenticacion
     *          Posibles Mensajes :
     *              Parámetro 'field' es obligatorio
     *              Parámetro 'value' es obligatorio
     *              Parámetros 'field' y 'value' son obligatorios
     *              Cliente no encontrado
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */
    public function actionSearch()
    {

        $data = \Yii::$app->request->post();

        if (empty($data['field']) && empty($data['value']) ) {
            \Yii::$app->response->setStatusCode(400);
            return [
                'error' => \Yii::t('ivrapi','"field" and "value" params are required')
            ];
        }


        if (empty($data['field'])) {
            \Yii::$app->response->setStatusCode(400);
            return [
                'error' => \Yii::t('ivrapi','"field" param is required')
            ];
        }

        if (empty($data['value'])) {
            \Yii::$app->response->setStatusCode(400);
            return [
                'error' => \Yii::t('ivrapi','"value" param is required')
            ];
        }

        $search = new CustomerSearch();
        $dataProvider = new ActiveDataProvider(['query' => $search->search($data)]);

        if ($dataProvider->count === 0 ) {
            \Yii::$app->response->setStatusCode(400);
            return  [
                'error' => \Yii::t('ivrapi','Customer not found')
            ];
        }

        return $dataProvider;

    }

    /**
     * @SWG\Post(path="/customer/balance-account",
     *     tags={"Customer"},
     *     summary="",
     *     description="Devuelve  el saldo del cliente, y la info del último pago",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "code",
     *        description = "Código del cliente",
     *        required = true,
     *        type = "integer"
     *     ),
     *
     *
     *     @SWG\Response(
     *         response = 200,
     *         description = "Devuelve  el saldo del cliente, y la info del último pago"
     *
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Parametro faltante, cliente no encontrado, o error de autenticacion
     *          Posibles Mensajes :
     *              Parámetro 'code' es obligatorio
     *              Cliente no encontrado
     *     ",
     *         @SWG\Schema(ref="#/definitions/Error1"),
     *     ),
     *
     * )
     *
     */
    public function actionBalanceAccount()
    {
        $data = \Yii::$app->request->post();

        if (!isset($data['code']) || empty($data['code'])) {
            \Yii::$app->response->setStatusCode(400);
            return [
                'error' => \Yii::t('ivrapi','"code" param is required')
            ];
        }

        $customer = Customer::findOne(['code' => $data['code']]);

        if (empty($customer)) {
            \Yii::$app->response->setStatusCode(400);
            return [
                'error' => \Yii::t('ivrapi','Customer not found')
            ];
        }

        return $customer->accountInfo();

    }
}