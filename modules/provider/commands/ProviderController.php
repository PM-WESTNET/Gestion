<?php

namespace app\modules\provider\commands;

use yii\console\Controller;
use app\modules\sale\models\TaxCondition;
use app\modules\provider\models\Provider;
use app\modules\provider\components\helpers\AfipHelper;
use app\modules\provider\components\helpers\InfoFileWriter;

class ProviderController extends Controller
{

    public function actionSanatizeProviders()
    {
        $providers_without_tax_condition = [];
        $providers_with_wrong_tax_condition = [];

        foreach ($this->getWrongProviders() as $provider) {
            if ($provider->tax_identification != '' || $provider->tax_identification != null) {
                $response = AfipHelper::afipValidation($provider->tax_identification);
                echo $response['status'];
                if ($response['status'] == true) {
                    $this->fillProviderData($response['data'], $provider);
                } else {
                    array_push($providers_with_wrong_tax_condition, $provider);
                }
            } else {
                array_push($providers_without_tax_condition, $provider);
            }
        }
        $this->fillProviderBusinessNames();
        $this->createFileWithoutTaxCondition($providers_without_tax_condition);
        $this->createFileWithWrongTaxCondition($providers_with_wrong_tax_condition);

        echo "\n Encontrá los archivos con el listado de proveedores que no han sido actulizados en la raiz del proyecto.\n";
    }

    public function getWrongProviders()
    {
        $tax_condition = TaxCondition::find()->where(['name' => 'Consumidor Final'])->one();
        $providers = Provider::find()->where(['tax_condition_id' => $tax_condition->tax_condition_id])->all();
//        $providers = Provider::find()->where(['provider_id' => [150, 154]])->all();
        return $providers;
    }

    public function getProvidersWithEmptyBusinessName()
    {
        $providers = Provider::find()->where(['business_name' => null])->all();
        return $providers;
    }

    public function fillProviderData($data, $provider)
    {
        if ($data['legal_name'] !== '') {
            if ($provider->business_name != $data['legal_name']) {
                $provider->updateAttributes(['business_name' => $data['legal_name']]);
            }
        } else {
            if ($provider->business_name != ($data['name'] . ' ' . $data['lastname'])) {
                $provider->updateAttributes(['business_name' => $data['name'] . ' ' . $data['lastname']]);
            }
        }
        if ($data['tax_id'] !== '' && $provider->tax_condition_id == 3) {
            if ($provider->tax_condition_id != $data['tax_id']) {
                $provider->updateAttributes(['tax_condition_id' => $data['tax_id']]);
            }
        }
        if ($data['address']['address'] !== '') {
            if ($provider->address == '' || $provider->address == null) {
                $provider->updateAttributes(['address' => $data['address']['province'] + ', ' + $data['address']['location'] + ', ' + $data['address']['address']]);
            }
        }
    }

    public function fillProviderBusinessNames()
    {
        $providers = $this->getProvidersWithEmptyBusinessName();
        foreach ($providers as $provider) {
            $provider->updateAttributes(['business_name' => $provider->name]);
        }
    }

    public function createFileWithoutTaxCondition($providers_without_tax_condition)
    {
        $fileName = 'Proveedores_sin_numero_de_identificacion.txt';
        $file_without_tax = new InfoFileWriter($providers_without_tax_condition);
        $file_without_tax->parse();
        $file_without_tax->writeFile($fileName, 'w+');
        echo "\n" . count($providers_without_tax_condition) . " proveedores no tienen identificador.\n";
    }

    public function createFileWithWrongTaxCondition($providers_with_wrong_tax_condition)
    {
        $fileName = 'Proveedores_con_numero_de_identificacion_erroneo.txt';
        $file_wrong_tax = new InfoFileWriter($providers_with_wrong_tax_condition);
        $file_wrong_tax->parse();
        $file_wrong_tax->writeFile($fileName, 'w+');
        echo "\n" . count($providers_with_wrong_tax_condition) . " proveedores no pudieron ser validados contra AFIP.\n";
    }

}