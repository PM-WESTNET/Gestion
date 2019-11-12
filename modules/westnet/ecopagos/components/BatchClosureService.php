<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 11/08/16
 * Time: 16:17
 */

namespace app\modules\westnet\ecopagos\components;


use app\modules\config\models\Config;
use app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderBillItem;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\models\BatchClosure;
use Yii;

/**
 * Class BatchClosureService
 *
 * Clase con servicios varios de BatchClosure.
 *
 * @package app\modules\westnet\ecopagos\components
 */
class BatchClosureService
{

    /** @var BatchClosureService $instance  */
    private static $instance = null;

    /** @var  string $messages */
    public $messages;

    /**
     * Retorno la instancia
     * @return BatchClosureService|null
     */
    public static function getInstance()
    {
        if(self::$instance === null){
            self::$instance = new BatchClosureService();
        }

        return self::$instance;
    }

    /**
     * Registra Facturas y notas de creditos.
     *
     * @param BatchClosure $batchClosure
     */
    public function registerBill(BatchClosure $batchClosure)
    {
        $this->messages = [];
        if(!$batchClosure){
            $this->messages[] = 'The batch closure can\'t be null.';
            return false;
        }

        $company_id = Config::getValue("ecopago_batch_closure_company_id");
        if(!$company_id) {
            $this->messages[] = 'The configuration of batch closure company is not defined.';
            return false;
        }
        $bill_type_id = Config::getValue("ecopago_batch_closure_bill_type_id");
        if(!$bill_type_id) {
            $this->messages[] = 'The configuration of bill type is not defined.';
            return false;
        }
        $credit_type_id = Config::getValue("ecopago_batch_closure_credit_type_id");
        if(!$credit_type_id) {
            $this->messages[] = 'The configuration of credit bill type is not defined.';
            return false;
        }
        $debit_type_id = Config::getValue("ecopago_batch_closure_debit_type_id");
        if(!$debit_type_id) {
            $this->messages[] = 'The configuration of debit bill type is not defined.';
            return false;
        }
        if(!$batchClosure->ecopago->provider) {
            $this->messages[] = 'The ecopago does not have provider configurated. Closure bill was not created. Please, create it manually.';
            return false;
        }

        try {
            $company = Company::findOne(['company_id'=>$company_id]);

            // Genero la factura
            $this->createBill(
                $bill_type_id,
                $company,
                $batchClosure->ecopago->provider_id,
                EcopagosModule::t("app", "Commision") . " - " .EcopagosModule::t("app", "Batch Closures") . ": " . $batchClosure->batch_closure_id,
                $batchClosure->commission
            );


            // Si rindio de mas la diferencia es negativa y le hago una nota de debito
            if($batchClosure->difference < 0){
                // Genero la factura
                $this->createBill(
                    $debit_type_id,
                    $company,
                    $batchClosure->ecopago->provider_id,
                    EcopagosModule::t("app", 'Positive balance') . " - " . EcopagosModule::t("app", "Batch Closures") . ": " . $batchClosure->batch_closure_id . " - ",
                    abs($batchClosure->difference)
                );
            } else if($batchClosure->difference > 0){ // Si rindio menos menos la diferencia es positiva
                $this->createBill(
                    $credit_type_id,
                    $company,
                    $batchClosure->ecopago->provider_id,
                    EcopagosModule::t("app", 'Negative balance') . " - " . EcopagosModule::t("app", "Batch Closures") . ": " . $batchClosure->batch_closure_id,
                    abs($batchClosure->difference)
                );
            }
            return true;
        } catch(\Exception $ex){
            $this->messages[] = $ex->getMessage();
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