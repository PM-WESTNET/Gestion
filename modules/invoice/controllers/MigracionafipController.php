<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 1/07/15
 * Time: 15:18
 */

namespace app\modules\invoice\controllers;


use app\modules\invoice\components\einvoice\afip\Migrate;
use app\modules\invoice\components\einvoice\ApiFactory;
use Yii;

class MigracionafipController extends \app\components\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionMigrar()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            $quien = Yii::$app->request->post("quien");
            $api = $this->getApi();

            $migrate = new Migrate("fev1");//$api->getServiceName());
            try {
                switch($quien) {
                    case "alicuotaIva":
                        $api->getAlicuotasIVA();
                        error_log(print_r($api->getResult(),1));
                        $migrate->genericType("alicuotaiva", $api->getResult());
                        break;

                    case "condicionIva":
                        $api->getCondicionesIVA();
                        $migrate->genericType("condicioniva", $api->getResult());
                        break;

                    case "moneda":
                        $api->getMonedas();
                        $monedas = $api->getResult();
                        $migrate->genericType("moneda", $monedas);
                        foreach($monedas as $moneda) {
                            $api->getCotizacionMoneda($moneda['id']);
                            $migrate->moneyQuotation($api->getResult());
                        }

                        break;

                    case "puntoDeVenta":
                        $api->getPuntosVenta();
	
                        $migrate->pointOfSale($api->getResult());
                        break;

                    case "tipoDeComprobante":
                        $api->getTiposComprobante();
                        $migrate->genericType("tipodecomprobante", $api->getResult());
                        break;

                    case "tipoDeDocumento":
                        $api->getTiposDocumento();
                        $migrate->genericType("tipodedocumento", $api->getResult());
                        break;

                    case "unidadDeMedida":
                        $api->getUnidadesMedida();
                        $migrate->genericType("unidademedida", $api->getResult());
                        break;
                }
            } catch(\Exception $ex){
                return [
                    'status' => 'error',
                    'errors' => $ex->getMessage()
                ];
            }


            return [
                'status'=>'success'
            ];
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionComprobante()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';

            $tipoComprobante = Yii::$app->request->post("tipoComprobante");
            $nroComprobante = Yii::$app->request->post("nroComprobante");
            $puntoDeVenta = Yii::$app->request->post("puntoDeVenta");

            $api = $this->getApi();
            $api->getComprobante($tipoComprobante, $nroComprobante, $puntoDeVenta);

            return [
                'status'    => ($api->hasErrors() ? 'error' : 'success'),
                'data'      => $api->getResult(),
                'errors'    => $api->getErrors()
            ];


        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function getApi()
    {
        // Obtengo la session para ver si tengo guardado algun token
        $session = Yii::$app->session;

        // Obtengo los parametros de factura electronica
        $params = Yii::$app->params['einvoice'];

        $api = ApiFactory::getInstance()->getApi("app\\modules\\invoice\\components\\einvoice\\afip\\fev1\\Fev1");// mtxca fev1
        //$api = ApiFactory::getInstance()->getApi("app\\modules\\invoice\\components\\einvoice\\afip\\mtxca\\Mtxca");// mtxca fev1
        $api->setTesting($params['testing']);
        if ($session->has("afip_token")) {
            $api->setToken($session->get("afip_token"));
        }

        // 30712901302 20274329417
        if(!$api->isTokenValid() ) {
            $authorize = $api->authorize($params['certificate'], $params['private'], $params['phrase']);
            $session->set("afip_token", $api->getToken());
        }
        if ($api->isTokenValid() || $authorize) {
            try{
                $api->connect();
                $api->setCustomer(['cuit'=>$params['cuit']]);

                return $api;
            } catch (\Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }
}
