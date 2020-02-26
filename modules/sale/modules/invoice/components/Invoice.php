<?php

namespace app\modules\sale\modules\invoice\components;

use app\modules\invoice\components\einvoice\ApiFactory;
use app\modules\sale\models\Bill;
use Yii;

/**
 * A traves de esta clase se vincula Bill del modulo sale con el modulo invoice
 * para la facturacion electronica.
 *
 * @author martin
 */
class Invoice {

    private static $instance;
    
    public static function getInstance(){
        
        if(!self::$instance){
            self::$instance = new Invoice;
        }
        
        return self::$instance;
        
    }

    public function invoice(Bill $bill)
    {
        // Obtengo la session para ver si tengo guardado algun token
        $session = Yii::$app->cache;

        // Obtengo los parametros de factura electronica
        $params = Yii::$app->params['einvoice'];
        $cuit = str_replace('-', '', $bill->company->tax_identification);

        $pointOfSale = $bill->getPointOfSale();
        if(!$pointOfSale){
            throw new \yii\web\HttpException(500, 'Point of sale not found.');
        }

        $api = ApiFactory::getInstance()->getApi($bill->invoiceClass->class);
        $api->setCompany($bill->company);
        $api->setTesting($params['testing']);
        $api->setUseOnline($params['use-online']);
        $api->setSaveCalls($params['save-calls']);
        if ($session->exists("afip_token")) {
            $api->setTokens($session->get("afip_token"));
        }
        $obs = array();
        $errors = array();
        $result = array();
        try{
            if (!$api->isTokenValid()) {
                $certificate = Yii::getAlias('@webroot') . '/' . $bill->company->certificate;
                $key = Yii::getAlias('@webroot') . '/' . $bill->company->key;
                $authorize = $api->authorize($certificate, $key, $bill->company->certificate_phrase);
                $session->set("afip_token", $api->getTokens());
            }
            if ($api->isTokenValid() || $authorize) {
                if($api->connect()) {
                    $bill->number = 1;
                    // Obtengo el ultimo comprobante autorizado para poder sumarle en 1
                    if ( $api->getUltimoComprobanteAutorizado($bill->billType->code, $pointOfSale->number) ) {
                        $ultimoComprobante = $api->getResult();
                        // Al utlimo comprobante le sumo 1
                        $bill->number = ($ultimoComprobante['numeroComprobante'] == 0 ? 0: $ultimoComprobante['numeroComprobante']) + 1;

                        if($api->run($bill)) {
                            $result = $api->getResult();
                            $result['numero'] = $bill->number;
                        } else {
                            $result = $api->getResult();
                            $errors[] = "Error";
                        }
                    }
                }
            }
        } catch (\Exception $ex) {
            $errors[] = [
                'code' => $ex->getCode(),
                'message' =>$ex->getMessage()
            ] ;
        }

        // Verifico si existe error, observacion o evento
        if($api->hasErrors()) {
            $errors = array_merge($errors, $api->getErrors());
        }

        if($api->hasObservations()) {
            $obs = $api->getObservations();
        }
        if($api->hasEvents()) {
            $obs = array_merge($obs, $api->getEvents());
        }

        return [
            'status' => (empty($errors)?'success':'error'),
            'errors' => $errors,
            'result' => $result,
            'observations' => $obs,
        ];
    }
}
