<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 16/03/16
 * Time: 10:34
 */

namespace app\modules\checkout\components;
use app\modules\checkout\models\PaymentPlan;
use app\modules\config\models\Config;
use app\modules\sale\components\BillExpert;
use app\modules\sale\models\Bill;
use app\modules\sale\models\BillType;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\models\search\BillTypeSearch;
use app\modules\sale\models\TaxRate;
use app\modules\sale\models\Unit;
use Yii;
use yii\base\Exception;

/**
 * Class PaymentPlanManager
 * Crea un plan de pagos, para ello tiene en cuenta:
 *  - Customer,
 *  - Fecha de primer cuota.
 *  - Monto a financiar.
 *  - Cantidad de cuotas
 *  - Porcentaje de Recargo o Descuento.
 *
 * Para crear el plan de pago se realizan los siguientes pazos:
 *  - Se crear una nota de credito por el monto final.
 *  - Se crean los items a pagar del plan de pago.
 *
 * @package app\modules\checkout\Components
 */
class PaymentPlanManager
{

    /** @var PaymentPlan $paymentPlan */
    private $paymentPlan;

    public function __construct(PaymentPlan $paymentPlan)
    {
        $this->paymentPlan = $paymentPlan;
    }

    /**
     * Creo el plan de pago, creando los items a facturar y creando la nota de credito
     *
     * @return bool
     */
    public function create()
    {
        $bOk = false;
        $transaction = Yii::$app->db->beginTransaction();
        if($this->paymentPlan) {
            try {
                $this->createItemsToInvoice();
                //La nota de credito se crea si el usuario eligio la opcion para crearla
               
                if ($this->paymentPlan->create_bill == '1') {
                   
                    $this->createBill();
                }                
                $transaction->commit();
                $bOk = true;
            } catch( \Exception $ex ) {
                $transaction->rollBack();
                throw new \Exception($ex->getMessage());
            }
        }
        return $bOk;
    }

    /**
     * Cancelo el plan de pago, Anulando los items a facturar y creando una nota de debito.
     *
     * @return bool
     */
    public function cancel()
    {
        $bOk = false;
        $transaction = Yii::$app->db->beginTransaction();
        if($this->paymentPlan) {
            try {
                $productsToInvoice = ProductToInvoice::findAll(['payment_plan_id'=>$this->paymentPlan->payment_plan_id]);

                /**
                 * @var  $key
                 * @var ProductToInvoice $productToInvoice
                 */
                foreach( $productsToInvoice as $key=>$productToInvoice) {
                    if($productToInvoice->can(ProductToInvoice::STATUS_CANCELED)) {
                        $productToInvoice->changeState(ProductToInvoice::STATUS_CANCELED);
                    }
                }

                $this->createBill(false);
                $this->paymentPlan->cancel();
                $transaction->commit();
                $bOk = true;
            } catch( \Exception $ex ) {
                $transaction->rollBack();
                throw new \Exception($ex->getMessage());
            }
        }
        return $bOk;
    }

    private function createBill($isCredit=true)
    {
        $company = $this->paymentPlan->customer->company;

        //Buscamos el tipo de comprobante de acuerdo al usuario
        $billTypeSearch = new BillTypeSearch();
        $billType = $billTypeSearch->searchForCustomer(
                'app\\modules\\sale\\models\\bills\\'.($isCredit ? 'Credit' : 'Debit' ),
                $this->paymentPlan->customer_id,
                $company->company_id
            );
        
        //Si no se encuentra un tipo de comprobante, hay que revisar la config de la empresa
        if(empty($billType)){
            throw new \yii\web\HttpException(500, Yii::t('app','Please, check the configuration of company {company} to continue. Current customer requires "{billType}.'
                , ['company' => $company->name,'billType' => $this->paymentPlan->customer->taxCondition->billTypesNames]) );
        }

        $unit = Unit::findOne(['1'=>1]);

        /** @var Bill $bill */
        $bill = BillExpert::createBill($billType->bill_type_id);
        $bill->company_id = $company->company_id;
        $bill->point_of_sale_id = $company->getDefaultPointOfSale()->point_of_sale_id;
        $bill->customer_id = $this->paymentPlan->customer_id;
        $bill->date = (new \DateTime('now'))->format('Y-m-d');
        $bill->status = 'draft';
        $bill->save(false);

        //Si es Credito, se asocia la factura al plan
        if($isCredit) {
            $this->paymentPlan->bill_id = $bill->bill_id;
            $this->paymentPlan->save(false);
        }

        $taxRate = TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code')]);
        
        if (!$taxRate){
            throw new \yii\web\HttpException(404, 'Wrong item configuration: default_tax_rate_code');
        }

        //Se agrega el detalle correspondiente
        $bill->addDetail([
            //'product_id' => $pti->contractDetail->product_id,
            'unit_id' => $unit->unit_id,
            'qty' => 1,
            //'type' => $pti->contractDetail->product->type,
            'unit_net_price' => ($this->paymentPlan->final_amount / (1+$taxRate->pct)) ,
            'unit_final_price' => $this->paymentPlan->final_amount,
            'concept' => Yii::t('app', 'Payment Plan') . ' - ' . $this->paymentPlan->payment_plan_id,
        ]);
        
        $bill->save(false);

        if(!$bill->close()){
            throw new \Exception( Yii::t('app', 'Can\'t close the Bill.') );
        }
    }

    private function createItemsToInvoice()
    {
        $taxRate = TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code')]);
        $total = $this->paymentPlan->final_amount / ($taxRate->pct + 1);
        $period = new \DateTime($this->paymentPlan->from_date);
        $period->modify('first day of this month');
        for($i=1; $i<=$this->paymentPlan->fee; $i++) {
            $ptb = new ProductToInvoice();
            $ptb->date = (new \DateTime('now'))->format('d-m-Y');
            $ptb->amount = ($total / $this->paymentPlan->fee);
            $ptb->status = ProductToInvoice::STATUS_ACTIVE;
            $ptb->period = $period->format('d-m-Y');
            $ptb->customer_id = $this->paymentPlan->customer_id;
            $ptb->payment_plan_id = $this->paymentPlan->payment_plan_id;
            $ptb->description = Yii::t('app', 'Payment Plan - Fee {fee} of {total}', ['fee'=>$i, 'total'=> $this->paymentPlan->fee] );
            $ptb->validate();
            if(empty($ptb->getErrors())) {
                $ptb->save();
                $period->modify('+1 month');
            } else {

                throw new \Exception( Yii::t('app', 'Can\'t create Items to Invoice') );
            }
        }
    }

}