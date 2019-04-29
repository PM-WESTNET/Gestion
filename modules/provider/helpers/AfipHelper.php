<?php

namespace app\modules\provider\components\helpers;

//use yii\web\Response;
use app\modules\invoice\components\einvoice\ApiFactory;
use app\modules\afip\components\CuitOnlineValidator;
use app\modules\sale\models\Company;

class AfipHelper
{

    public static function afipValidation($document)
    {
        $params = \Yii::$app->params['afip-validation'];

        /** @var CuitOnlineValidator $api */
        $api = ApiFactory::getInstance()->getApi(CuitOnlineValidator::class);
        $company = Company::findOne(['company_id' => $params['company_id']]);

        $valid_data = '';
        $final_data = '';
        $errors = [];

        $api->setCompany($company);
        $api->setTesting($params['testing']);
        $api->setUseOnline($params['use-online']);
        $api->setSaveCalls($params['save-calls']);

        try {
            if (!$api->isTokenValid()) {
                $certificate = \Yii::getAlias('@webroot') . '/' . $company->certificate;
                $key = \Yii::getAlias('@webroot') . '/' . $company->key;
                $authorize = $api->authorize($certificate, $key, $company->certificate_phrase);
            }
            if ($api->isTokenValid() || $authorize) {
                error_log("4");

                if ($api->connect([], ["ssl" => ["ciphers" => "TLSv1"]], 'SOAP_1_1')) {
                    error_log("5");
                    \Yii::trace('se conecta a api');
                    $valid_data = $api->validate(str_replace('-', '', $document));
                    $final_data = $api->extractData($valid_data);
                }
            }
        } catch (\Exception $ex) {
            $errors[] = [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ];
        }

        return [
            'status' => $valid_data ? true : false,
            'data' => $final_data,
            'errors' => $errors
        ];
    }

}