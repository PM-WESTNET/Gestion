<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 30/09/16
 * Time: 12:34
 */

namespace app\modules\westnet\components;


use app\modules\config\models\Config;
use app\modules\provider\models\ProviderBill;
use app\modules\sale\models\Company;
use app\modules\westnet\models\Vendor;
use app\modules\westnet\models\VendorLiquidation;
use Yii;

class VendorLiquidationService
{


    /** @var VendorLiquidationService $instance  */
    private static $instance = null;

    /** @var  string $messages */
    public $messages;

    /**
     * Retorno la instancia
     * @return VendorLiquidationService|null
     */
    public static function getInstance()
    {
        if(self::$instance === null){
            self::$instance = new VendorLiquidationService();
        }

        return self::$instance;
    }

    /**
     * Registra Facturas y notas de creditos.
     *
     * @param VendorLiquidation $batchClosure
     */
    public function registerBill(VendorLiquidation $vendorLiquidation)
    {
        $this->messages = [];
        if(!$vendorLiquidation){
            $this->messages[] = 'The liquidation can\'t be null.';
            return false;
        }

        $company_id = Config::getValue("ecopago_batch_closure_company_id");
        if(!$company_id) {
            $this->messages[] = 'The configuration of Vendor Liquidation Company is not defined.';
            return false;
        }
        $bill_type_id = Config::getValue("ecopago_batch_closure_bill_type_id");
        if(!$bill_type_id) {
            $this->messages[] = 'The configuration of bill type is not defined.';
            return false;
        }

        if(!$vendorLiquidation->vendor->provider) {
            $this->messages[] = 'The vendor does not have provider configurated. Liquidation bill was not created. Please, create it manually.';
            return false;
        }

        try {
            $company = Company::findOne(['company_id'=>$company_id]);

            // Genero la factura
            $this->createBill(
                $bill_type_id,
                $company,
                $vendorLiquidation->vendor->provider_id,
                Yii::t("app", "Commision") . " - " . $vendorLiquidation->vendor->getFullName() .  ": " . $vendorLiquidation->vendor_liquidation_id,
                $vendorLiquidation->getTotal()
            );

            $vendorLiquidation->status = VendorLiquidation::VENDOR_LIQUIDATION_BILLED;
            $vendorLiquidation->update(false);

            return true;
        } catch(\Exception $ex){
            $this->messages[] = $ex->getTraceAsString();
            return false;
        }

    }

    private function createBill($type_id, Company $company, $provider_id, $description, $amount)
    {
        /** @var ProviderBill $bill */
        $bill = new ProviderBill();
        $bill->bill_type_id = $type_id;
        $bill->date = (new \DateTime('now'))->format('d-m-Y');
        $bill->company_id = $company->company_id;
        $bill->provider_id = $provider_id;
        $bill->partner_distribution_model_id = $company->partner_distribution_model_id;
        $bill->net = $amount;
        $bill->status = 'draft';
        $bill->save();
        $bill->addItem([
            'provider_bill_id'=> $bill->provider_bill_id,
            'account_id'=> null,
            'amount'=> $amount,
            'description'=> $description,
        ]);

        $bill->close();
    }
}