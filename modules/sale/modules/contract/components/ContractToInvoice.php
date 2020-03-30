<?php

namespace app\modules\sale\modules\contract\components;

use app\components\helpers\EmptyLogger;
use app\components\helpers\FlashHelper;
use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\sale\components\BillExpert;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\models\Discount;
use app\modules\sale\models\InvoiceProcess;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\search\ProductToInvoiceSearch;
use app\modules\sale\models\TaxRate;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\sale\modules\contract\models\search\ContractSearch;
use app\modules\westnet\isp\IspFactory;
use app\modules\westnet\models\Connection;
use app\modules\westnet\sequre\components\request\ContractRequest;
use Codeception\Util\Debug;
use DateTime;
use Exception;
use Yii;

/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 26/11/15
 * Time: 15:48
 */
class ContractToInvoice
{

    private $_discounts = [];

    private $messages = [];

    private $invoice_day_for_next_month;

    private $numbers = [];

    public function getBillNumber($bill_type_id, $company_id)
    {
        if(array_key_exists($bill_type_id."-".$company_id, $this->numbers)) {
            $lastNumber = $this->numbers[$bill_type_id."-".$company_id] + 1;
        } else {
            $lastNumber = Bill::find()->where([
                    'bill_type_id' => $bill_type_id,
                    'status' => 'closed',
                    'company_id' => $company_id
                ])->max('number') + 1;
        }
        $this->numbers[$bill_type_id."-".$company_id] = $lastNumber;
        return $lastNumber;
    }


    /**
     * Actualizo el contrato,
     * @param Contract $contract
     * @return bool
     */
    public function updateContract(Contract $contract)
    {
        try{
            // Guardo los proximos a |urar.
            foreach ($contract->contractDetails as $detail) {
                $updateAttributes = ['from_date', 'status'];
                // Si es plan no tengo que guardarlo
                if ($detail->status == Contract::STATUS_DRAFT) {
                    $contractStart = (Yii::t('app', 'Undetermined time') == $contract->from_date ? null : (new DateTime($contract->from_date))); ;

                    // Defino al periodo en el primer dia del mes de inicio del contrato
                    // Si el item tiene distinta fecha, tomo la del item
                    $detailDate = (Yii::t('app', 'Undetermined time') == $detail->from_date ? null : (new DateTime($detail->from_date))); ;
                    //if( $detailDate && $contractStart != $detailDate ) {
                    if( $contract->status == Contract::STATUS_ACTIVE ) {
                        $period = new DateTime($detailDate->format('Y-m-') . '01');
                    } else {
                        $period = new DateTime('first day of this month');
                    }

                    if ($detail->canAddProductToInvoice($period)) {
                        // A cada detalle le pongo la fecha de inicio de contrato
                        $detail->from_date = $period->format('Y-m-d');
                        // Si no es plan cargo los ProductToBill
                        try {
                            $this->invoice_day_for_next_month = Config::getValue('contract_days_for_invoice_next_month');
                        } catch (\Exception $ex) {
                            $this->invoice_day_for_next_month = 0;
                        }

                        // Si esta dentro de los dias gratis, el periodo pasa al siguiente mes
                        if ($this->invoice_day_for_next_month != 0 && ( (new DateTime('now'))->format('Ym') == $contractStart->format('Ym')  &&
                            $this->invoice_day_for_next_month <= $contractStart->format('d') ) ) {
                            $period = $period->modify('first day of next month');

                        }
                        // Si tiene plan de pago tengo que crear una cuota para cada mes.
                        if ($detail->funding_plan_id) {
                            for ($i = 1; $i <= $detail->fundingPlan->qty_payments; $i++) {
                                $ptb = $this->createProductToInvoice([
                                    'contract_detail_id' => $detail->contract_detail_id,
                                    'funding_plan_id' => $detail->funding_plan_id,
                                    'date' => (new DateTime('now'))->format('d-m-Y'),
                                    'amount' => $detail->fundingPlan->amount_payment,
                                    'status' => Contract::STATUS_ACTIVE,
                                    'period' => $period->format('d-m-Y'),
                                    'qty'    => $detail->count,
                                    'discount_id' => ($detail->discount ? $detail->discount->discount_id : null )
                                ]);
                                $period->modify('first day of next month');

                                // Si es la ultima cuota le pongo al item la fecha del ultimo periodo
                                // como la fecha de fin.
                                if ($i == $detail->fundingPlan->qty_payments) {
                                    $detail->to_date = $period->format('d-m-Y');
                                }
                                $ptb->save(true);
                            }
                        } else {
                            //Solicito el precio activo en funcion del contrato de cliente (aplica reglas de negocio)
                            $activePrice = $detail->product->getActivePrice($detail)->one();

                            // Es sin plan de pago, genero para el preiodo que coresponde y sigo.
                            $ptb = $this->createProductToInvoice([
                                'contract_detail_id' => $detail->contract_detail_id,
                                'funding_plan_id' => $detail->funding_plan_id,
                                'date' => (new DateTime('now'))->format('d-m-Y'),
                                'amount' => $activePrice->net_price,
                                'status' => Contract::STATUS_ACTIVE,
                                'period' => $period->format('d-m-Y'),
                                'qty' => $detail->count,
                                'discount_id' => ($detail->discount ? $detail->discount->discount_id : null )
                            ]);
                            $ptb->save(true);

                            if ($ptb->hasErrors()){
                                Debug::debug('Errores en PTB: ' . print_r($ptb->getErrors(),1));
                            }
                        }
                        if ($detail->product->type != 'plan') {
                            $detail->to_date = clone $period;
                            $detail->to_date = $detail->to_date->modify('+1 month')->modify('-1 day')->format('Y-m-d');
                            $updateAttributes[] = 'to_date';
                        }
                    }
                    $detail->status = Contract::STATUS_ACTIVE;
                    $detail->updateAttributes($updateAttributes);
                }
            }
            return true;
        } catch (Exception $ex){
            Debug::debug('Excepcion sale por false: ' . $ex->getTraceAsString());
            Yii::info($ex->getTraceAsString(), 'Active_Contract');
            error_log($ex->getMessage());
            return false;
        }
    }

    /**
     * Crea el contrato y las cuotas que correspondan, dejando los estado en active.
     *
     * @param $contract Contract
     * @return boolean
     */
    public function createContract(Contract $contract, Connection $connection = null)
    {
        // Inicializo transaccion
        $transaction = $contract->db->beginTransaction();
        try {
            
            //Requerido!
            $connection->refresh();
            
            // Actualizo el contrato
            $this->updateContract($contract);

            // Actualizo la conexion
            $this->updateConnection($contract->contract_id, $connection);

            // Actualizo la empresa del cliente.
            /** @var Customer $customer */
            $customer = $contract->customer;
            CompanyByNode::setCompanyToCustomer($connection->node, $customer);

            // Actulizo contrato y ejecuto behaviors de conexion y demas.
            $contract->status = Contract::STATUS_ACTIVE;
            $behavior = $contract->getBehavior('status');
            $contract->detachBehavior('status');
            $contract->save(false, ['status']);
            $transaction->commit();
            $contract->attachBehavior('status', $behavior);
            return true;
        } catch (Exception $ex) {
            error_log($ex->getTraceAsString());
            $transaction->rollBack();
        }
        return false;
    }

    /**
     * Acualizo la conexion.
     *
     * @param $contract_id
     * @param $connection
     */
    public function updateConnection($contract_id, Connection $connection)
    {
        if($connection) {
            $connection->contract_id = $contract_id;
            $connection->updateIp();
            $connection->server_id = $connection->node->server_id;
            $connection->due_date = null;
            $connection->status_account = Connection::STATUS_ACCOUNT_DISABLED;
            if($connection->use_second_ip) {
                $connection->ip4_2 =  $connection->ip4_1+1;
            }
            try{
                $connection->save();
            } catch(Exception $ex) {
            }
        }
    }

    private function createProductToInvoice($params)
    {
        $ptb = new ProductToInvoice();
        $ptb->contract_detail_id = $params['contract_detail_id'];
        $ptb->funding_plan_id = $params['funding_plan_id'];
        $ptb->date = $params['date'];
        $ptb->amount = $params['amount'];
        $ptb->status = Contract::STATUS_ACTIVE;
        $ptb->period = $params['period'];
        $ptb->qty = $params['qty'];
        if(array_key_exists('discount_id', $params)){
            $ptb->discount_id = $params['discount_id'];
        }
        return $ptb;
    }

    /**
     * Cancela el contrato y las cuotas que correspondan, dejando los estado en canceled.
     *
     * @param $contract Contract
     * @return boolean
     */
    public function cancelContract($contract)
    {
        if ($contract->status != Contract::STATUS_LOW_PROCESS) {
            return false;
        }
        // Inicializo transaccion
        $transaction = $contract->db->beginTransaction();
        try {

            $contract->status = Contract::STATUS_LOW;
            // Guardo los proximos a facturar.
            foreach ($contract->contractDetails as $detail) {
                $detail->status = $contract->status;
                $detail->to_date = (new \Datetime($contract->to_date))->format('Y-m-d');
                $detail->updateAttributes(['status', 'to_date']);

                foreach ($detail->productToInvoices as $pti) {
                    if ((new DateTime($contract->to_date))->diff((new DateTime($pti->period)))->format('%r%a') >= 0) {
                        $pti->status = $contract->status;
                        $pti->updateAttributes(['status']);
                    }
                }
            }
            
            $contract->updateAttributes(['status']);
                
            

            $connection = Connection::findOne(['contract_id'=>$contract->contract_id]);
            $connection->ip4_1 = 0;
            $connection->ip4_2 = 0;
            $connection->status= Connection::STATUS_LOW;
            $connection->status_account= Connection::STATUS_ACCOUNT_LOW;
            $connection->updateAttributes(['ip4_1', 'ip4_2', 'status', 'status_account']);

            //Elimino el contrato del servidor ISP que sea
            // Como existe la conexion, creo los request
            $api = IspFactory::getInstance()->getIsp($connection->server);
            $contractRequest    = $api->getContractApi();
            $contractRequest->delete($contract->external_id);
            
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            error_log($ex->getMessage());
        }
        return false;
    }

    public function invoiceAll($params)
    {
//        Yii::setLogger(new EmptyLogger());

        $bill_observation = array_key_exists('bill_observation', $params) ? $params['bill_observation'] : '';
        $contractSearch = new ContractSearch();
        $contractSearch->setScenario('for-invoice');
        $time_start = microtime(true);

        echo 'InvoiceAll() - contractToInvoice comienza en ' .$time_start ."\n";
        //1.0
        $cantidadTotal = $contractSearch->searchForInvoice($params)->count();

        //2.0
        echo 'InvoiceAll() - Despues de searchForInvoice query ---- '. microtime(true)."\n";

        $paginas = ceil($cantidadTotal/100);
        $company = Company::findOne($contractSearch->company_id);

        $period = new DateTime( $contractSearch->period );
        if( $contractSearch->invoice_date instanceof DateTime) {
            $invoice_date = $contractSearch->invoice_date;
        } else {
            $invoice_date = DateTime::createFromFormat( 'd-m-Y', $contractSearch->invoice_date);
        }
        $customers = [];

        try {
            $this->invoice_day_for_next_month = Config::getValue('contract_days_for_invoice_next_month');
        } catch (\Exception $ex) {
            $this->invoice_day_for_next_month = 0;
        }

        $time_start = (microtime(true) - $time_start);
        echo 'InvoiceAll() - valores por defecto, params  ---- '. $time_start."\n";

        //3.0
        $i = 1;
        Yii::$app->cache->set( '_invoice_all_', [
            'total' => $cantidadTotal,
            'qty'   => $i
        ]);

        $time_start = (microtime(true) - $time_start);
        echo 'InvoiceAll() - setear en cache  ---- '. $time_start."\n";

        // Creo notificacion para la app. Es una notificacion para todos los clientes que se facturen
//        $mobilepush = $this->createMobilePush();

        $afip_error = false;


        //ECHO INNECESARIO DE LA MISMA QUERY
        //echo count($contractSearch->searchForInvoice($params)->all()) . "\n";

        // 4.0
        //Se hace el cambio a batch para evitar facturacion de contratos duplicados
        foreach($contractSearch->searchForInvoice($params)->batch() as $contractList) {
            //4.1.0
            foreach($contractList as $item) {
                $time_start = (microtime(true) - $time_start);
                echo "InvoiceAll() - Iteracion en batch ---- ". $time_start."\n";

                $transaction = Yii::$app->db->beginTransaction();
                if( array_search($item['customer_id'],  $customers ) === false ) {
                    $time_start = (microtime(true) - $time_start);
                    echo "InvoiceAll() - antes de invoice ---- ". $time_start."\n";

                    //4.1.1
                    if(!$this->invoice($company, $contractSearch->bill_type_id, $item['customer_id'], $period, true, $bill_observation, $invoice_date, false, true) ) {
                        $afip_error = true;
                    }

                    $time_start = (microtime(true) - $time_start);
                    echo "InvoiceAll() - despues de invoice ---- ". $time_start."\n";

                    //4.1.2
                    Yii::$app->cache->set('_invoice_all_', [
                        'total' => $cantidadTotal,
                        'qty' => $i
                    ]);

                    $time_start = (microtime(true) - $time_start);
                    echo "InvoiceAll() - despues de setear en cache ---- ". $time_start."\n";

                }
                /*if($afip_error) {
                    $transaction->rollBack();
                    //break;
                } else {*/
                    $customers[] = $item['customer_id'];

                    // Agrego al cliente a la notificacion, para que se le notifique
//                    $this->addCustomerToMobilePush($mobilepush, $item['customer_id']);

//                    Yii::$app->session->close();
                    $i++;
                    $transaction->commit();
                //}
            }
        }

        //Actualizo el estado de el registro de proeso de facturación
        InvoiceProcess::endProcess(InvoiceProcess::TYPE_CREATE_BILLS);

        // Envio la notificacion a los clientes facturados
//        $mobilepush->send();

    }

    /**
     * @param $company
     * @param $bill_type_id
     * @param $customer_id
     * @param $period
     * @param bool $includePlan
     * @return boolean
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public function invoice($company, $bill_type_id, $customer_id, $period, $includePlan=true, $bill_observation = '', $invoice_date = null, $close_bill = true, $automatically_generated = false)
    {

        try{

            if ($company && $bill_type_id && $customer_id) {
                $node = null;
                /** @var Bill $bill */


                //4.1.1.1
                $start = microtime(true);
                echo "invoice() - Antes de crear bill---- 4.1.1.1 ". $start."\n";

                $bill = BillExpert::createBill($bill_type_id);
                $bill->company_id = $company->company_id;
                $bill->point_of_sale_id = $company->getDefaultPointOfSale()->point_of_sale_id;
                $bill->customer_id = $customer_id;
                $bill->date = ($invoice_date ? $invoice_date->format('Y-m-d') : $period->format('Y-m-d') );
                $bill->status = 'draft';
                $bill->observation = $bill_observation;
                $bill->automatically_generated = $automatically_generated ? true : null;
                $bill->save(false);

                //4.1.1.2
                echo "invoice() - Creacion de bill 4.1.1.2 ". (microtime(true) - $start) ."\n";
                $start = microtime(true);

                // Como ya no tengo el contrato, busco todos los contratos para el customer
                $contractSearch = new ContractSearch();
                $contractSearch->setScenario('for-invoice');
                $contractSearch->company_id = $company->company_id;
                $contractSearch->customer_id = $customer_id;
                $contractSearch->bill_type_id = $bill_type_id;
                $contractSearch->period = $period;
                $contracts = $contractSearch->searchForInvoice([], true, $includePlan)->all();

                //4.1.1.3
                echo "invoice() - SearchforInvoice() /4.1.1.3" . (microtime(true)-$start)."\n";
                $start = microtime(true);

                // Busco el customer que estoy procesando
                $customer = Customer::findOne($customer_id);
                // Traigo todos los descuentos aplicados al customer
                // Se tienen en cuenta por la fecha, mas alla de los periodos aplicados.
                $customerActiveDiscount = $customer->getActiveCustomerHasDiscounts($period)->all();

                //4.1.1.4
                echo "invoice() - Busqueda de descuentos 4.1.1.4 " . (microtime(true) - $start)."\n";
                $start = microtime(true);


                $next = false;
                $default_unit_id = Config::getValue('default_unit_id');

                //4.1.1.5
                echo "invoice() - Inicio de iteracion de contratos 4.1.1.5 " . (microtime(true) - $start)."\n";
                $start = microtime(true);

                foreach ($contracts as $contract_value) {
                    $contract = Contract::findOne(['contract_id' => $contract_value['contract_id']]);
                    $contractStart = new DateTime( Yii::$app->formatter->asDate($contract->from_date)) ;

                    //4.1.1.5.1
                    echo "invoice() - Iteracion de contratos  4.1.1.5.1 ". (microtime(true) - $start)."\n";
                    $start = microtime(true);

                    $periods[] = $period;

                    // Si el mes y año de inicio es igual al actual y la fecha de inicio esta dentro de los dias libres
                    // agrego el mes siguiente para facturar ya que seguramente los productos asignados van a estar ahi
                    if ($this->invoice_day_for_next_month != 0 && ( (new DateTime('now'))->format('Ym') == $contractStart->format('Ym'))  &&
                        $this->invoice_day_for_next_month <= $contractStart->format('d')
                    ){
                        $next = true;
                        $nextPeriod = clone $period;
                        $periods[] = $nextPeriod->modify('first day of next month');
                    }

                    // Verifico que el plan tenga item a facturar, en caso de no tener agrego los Planes
                    foreach($contract->contractDetails as $contractDetail) {

                        //4.1.1.5.1.1
                        echo "invoice() - iteracion de contract detail 4.1.1.5.1.1 " . (microtime(true) - $start)."\n";
                        $start = microtime(true);

                        if($contractDetail->product->type == 'plan' && $includePlan) {
                            //4.1.1.5.1.1.1
                            echo "invoice() - iteracion de contract detail 4.1.1.5.1.1.1 " . (microtime(true) - $start)."\n";
                            if (!$contractDetail->isAddedForInvoice($periods)){
                                //4.1.1.5.1.1.2
                                echo "invoice() - iteracion de contract detail 4.1.1.5.1.1.2 " . (microtime(true) - $start)."\n";
                                $discount = $this->getDiscount($contractDetail->product_id, $customerActiveDiscount, true);

                                //4.1.1.5.1.1.3
                                echo "invoice() - iteracion de contract detail 4.1.1.5.1.1.3 " . (microtime(true) - $start)."\n";
                                //Solicito el precio activo en funcion del contrato de cliente (aplica reglas de negocio)
                                $activePrice = $contractDetail->product->getActivePrice($contractDetail)->one();

                                $pti = $this->createProductToInvoice([
                                    'contract_detail_id' => $contractDetail->contract_detail_id,
                                    'funding_plan_id' => null,
                                    'date' => (new DateTime('now'))->format('d-m-Y'),
                                    'amount' => $activePrice->net_price,
                                    'status' => 'active',
                                    'period' => ($next ? $nextPeriod->format('d-m-Y') : $period->format('d-m-Y')),
                                    'discount_id' => ($discount ? $discount->discount_id : null ),
                                    'customer_id' => $contract->customer_id,
                                    'qty' => 1,
                                ]);
                                $pti->save(false);
                            }
                        }

                        //4.1.1.5.1.2
                        echo "invoice() - Fin iteracion de contract detail 4.1.1.5.1.2 " . (microtime(true) - $start)."\n";
                        $start = microtime(true);

                    }

                    //4.1.1.5.2
                    echo "invoice() - Inicio de busqueda de product to invoice 4.1.1.5.2 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);

                    // Itero en los items a facturar y voy agregandolo a la factura
                    $search = new ProductToInvoiceSearch();
                    $products_to_invoice = $search->search($periods, $contract->contract_id, $contract->customer_id)->all();

                    //4.1.1.5.3
                    echo "invoice() - Fin de busqueda de product to invoice 4.1.1.5.3 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);

                    /** @var ProductToInvoice $pti */
                    foreach($products_to_invoice as $pti) {

                        //4.1.1.5.3.1
                        echo "invoice() - Inicio de iteracion  de product to invoice 4.1.1.5.3.1 " . (microtime(true) - $start)."\n";
                        $start = microtime(true);

                        // Veo si tiene una categoria que me cambie el importe de facturacion
                        // Y el factor es que voy a multiplicar por el neto a facturar
                        $factor = 1;
                        if ($contract->customer->getCustomerClass()) {
                            $factor = ($contract->customer->customerClass->percentage_bill / 100);
                        }

                        // Aca tengo el neto, sin descuentos e impuestos
                        $unit_net_price     = ($pti->amount  * $factor);


                        // El proporcional se calcula si el mes de inicio del plan es igual al mes que se esta.
                        if($pti->contract_detail_id) {
                            if($pti->contractDetail->product->type == 'plan') {
                                if($includePlan) {
                                    $factorProporcional = 1;
                                    if( $period->format('Ym') == $contractStart->format('Ym') && !$next) {
                                        $factorProporcional = (30 - $contractStart->format('d'))/30;
                                    }
                                    $unit_net_price     = ($unit_net_price  * $factorProporcional);
                                } else {
                                    continue;
                                }
                            }
                        }

                        //4.1.1.5.3.2
                        echo "invoice() - Antes de buscar descuento a item " . (microtime(true) - $start)."\n";
                        $start = microtime(true);

                        // Si el item tiene descuento lo busco y aplico
                        $discount = null;
                        $unit_net_discount = 0;
                        if ($pti->discount) {
                            $discount = $pti->discount;
                        } else {
                            // Verifico que el producto tenga un descuento aplicado
                            if($pti->contract_detail_id && $pti->contractDetail->product_id) {
                                // Busco los descuentos y solo paso el primero que encuentro
                                $discounts = Discount::findActiveByProduct($pti->contractDetail->product_id);
                                $discount = (count($discounts)>0 ?  $discounts[0]: null );
                            }
                        }

                        //4.1.1.5.3.3
                        echo "invoice() - Despues de buscar descuento a item 4.1.1.5.3.3 " . (microtime(true) - $start)."\n";
                        $start = microtime(true);

                        if($discount) {
                            if($discount->type == Discount::TYPE_PERCENTAGE ) {
                                $unit_net_discount =  $unit_net_price * ($discount->value/100);
                            } else {
                                $unit_net_discount =  $discount->value;
                            }

                            //Si el descuento es de tipo persistente, se debe deshabilitar una vez que ha sido aplicado.
                            if($discount->persistent) {
                                $customer_discount = CustomerHasDiscount::find()->where(['discount_id' => $discount->discount_id, 'customer_id' => $bill->customer_id, 'status' => CustomerHasDiscount::STATUS_ENABLED])->one();
                                if($customer_discount) {
                                    $customer_discount->updateAttributes(['status' => CustomerHasDiscount::STATUS_DISABLED]);
                                }
                            }
                        }

                        //4.1.1.5.3.4
                        echo "invoice() - Despues de aplicar descuento a item 4.1.1.5.3.4 ". (microtime(true) - $start)."\n";
                        $start = microtime(true);

                        $unit_net_price_with_discount = $unit_net_price - $unit_net_discount;
                        // Calculo el total unitario en base al importe con descuento
                        if($pti->contractDetail) {
                            $unit_final_price = $unit_net_price_with_discount + $pti->contractDetail->product->calculateTaxes($unit_net_price_with_discount);
                        } else {
                            $taxRate = TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code')]);
                            if($taxRate) {
                                $unit_final_price = $unit_net_price_with_discount + $taxRate->calculate($unit_net_price_with_discount);
                            } else {
                                $unit_final_price = $unit_net_price_with_discount;
                            }
                        }

                        //POSIBLE OPTIMIZACION
                        // $contractDetail = $pti->contractDetail;
                        $bill->addDetail([
                            'product_id' => ($pti->contractDetail ? $pti->contractDetail->product_id : null ),
                            'unit_id' => ($pti->contractDetail ? $pti->contractDetail->product->unit_id : $default_unit_id ),
                            'qty' => $pti->qty,
                            'type' => ($pti->contractDetail ? $pti->contractDetail->product->type : null ),
                            'unit_net_price' => $unit_net_price,
                            'unit_final_price' => $unit_final_price,
                            'concept' => ($pti->contractDetail ? ($pti->contractDetail->product->description ?: $pti->contractDetail->product->name) . ($pti->contractDetail->product->type =='plan' ? ' - ' . Yii::$app->formatter->asDate($pti->period, 'MM/Y') : '' ) :
                                $pti->description ) ,
                            'discount_id' => ($discount ? $discount->discount_id : null ),
                            'unit_net_discount' => $unit_net_discount
                        ]);
                        $pti->status = 'consumed';

                        //4.1.1.5.3.5
                        echo "invoice() - Despuues de agregar el detalle a la factura 4.1.1.5.3.5 " . (microtime(true) - $start)."\n";
                        $start = microtime(true);

                        if (!$pti->save(false)) {
                            FlashHelper::flashErrors($pti);
                        }

                        //4.1.1.5.3.6
                        echo "invoice() - Fin de iteracion  de product to invoice 4.1.1.5.3.6 " . (microtime(true) - $start)."\n";
                        $start = microtime(true);
                    }

                    // Itero en los descuentos aplicados al cliente.
                    //4.1.1.5.4
                    echo "invoice() - Antes de agregar descuento 4.1.1.5.4 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);

                    foreach($customerActiveDiscount as $key => $customerDiscount) {

                        //POSIBLE OPTIMIZACION
                        //$discount = $customerDiscount->discount;
                        if($customerDiscount->discount->value_from == Discount::VALUE_FROM_TOTAL) {
                            $unit_net_discount = ( $customerDiscount->discount->type == Discount::TYPE_FIXED ?
                                $customerDiscount->discount->value
                                :
                                $bill->total * (((double)$customerDiscount->discount->value) / 100)
                            );

                            // Agrego un nuevo item por el total
                            $billDetail = $bill->addDetail([
                                'qty' => 1,
                                'unit_id' => 1,
                                'type' => 'discount',
                                'unit_net_price' => 0,
                                'unit_final_price' => 0,
                                'unit_net_discount' => $unit_net_discount,
                                'concept' => $customerDiscount,
                                'discount_id' => $customerDiscount->discount->discount_id
                            ]);

                            //Si el descuento es de tipo persistent y ya se aplicó se debe actualizar el estado
                            if($customerDiscount->discount->persistent) {
                                $customerDiscount->updateAttributes(['status' => CustomerHasDiscount::STATUS_DISABLED]);
                            }
                        }
                        unset($customerActiveDiscount[$key]);
                    }

                    //4.1.1.5.5
                    echo "invoice() - Despues de agregar descuento 4.1.1.5.5 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);
                }

                //4.1.1.6
                echo "invoice() - Antes de Verificacion de items en factura 4.1.1.6 " . (microtime(true) - $start)."\n";
                $start = microtime(true);

                if($bill->getBillDetails()->exists()) {
                    $bill->number = $this->getBillNumber($bill_type_id, $bill->company_id);
                    //4.1.1.6.1
                    echo "--- invoice() - Antes de save de bill 4.1.1.6.1 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);
                    $bill->save(false);
                    //4.1.1.6.2
                    echo "--- invoice() - Despues de save de bill 4.1.1.6.2 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);
                    $bill->fillNumber = false;
                    $bill->verifyAmounts(true);
                    //4.1.1.6.3
                    echo "--- invoice() - Verify amounts de bill 4.1.1.6.3 " . (microtime(true) - $start)."\n";
                    $start = microtime(true);

                    if($close_bill) {
                        $bill->complete();
                        $bill->close();
                    }
                }

                //4.1.1.7
                echo "invoice() - Despues de Verificacion de items en factura 4.1.1.7 " . (microtime(true) - $start)."\n";
                $start = microtime(true);


                // Si es electronica y no se emitio es por error en AFIP y corto proceso.
//                if($bill->getPointOfSale()->electronic_billing && $bill->status != 'closed') {
//                    $this->messages['error'][] = Yii::t('app', 'The billing process is stopped by problems with AFIP.');
//                    return false;
//                }

                $this->addMessage($bill);

            }

            return true;
        } catch(\Exeption $ex) {
            if (Yii::$app instanceof Yii\console\Application) {
                Yii::$app->cache->set('_invoice_create_errors', $ex->getTraceAsString());
            }
            error_log('*********************************************************************************');
            error_log( "Exception: " .  $ex->getMessage() );
        }
        return false;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    private function addMessage($bill, $type='error', $message=null)
    {
        if($message) {
            $this->messages[$type][] = $message;
        } else {
            // Busco los errores del comprobante y los agrego a un array local
            if (!Yii::$app instanceof Yii\console\Application) {
                $messages = Yii::$app->session->getAllFlashes();
                foreach($messages as $key=>$message) {
                    $this->messages[$key][] = Yii::t('app', 'The bill of {customer} can\'t be closed, reason: {reason}', [
                        'customer'=> $bill->customer->name, 'reason' => (is_array($message) ? implode('<br/>', $message ) : $message)
                    ]);
                }
            } else {
                if(Yii::$app->cache->get('_invoice_create_errors') ) {
                    foreach (Yii::$app->cache->get('_invoice_create_errors') as $error) {
                        $this->messages['danger'][] = Yii::t('app', 'The bill of {customer} can\'t be closed, reason: {reason}. Error: {error}', [
                            'customer'=> $bill->customer->name, 'reason' => (is_array($message) ? implode('<br/>', $message ) : $message) , 'error' => $error
                        ]);
                    }
                }
            }


        }
    }

    private function getProductDiscount($product_id)
    {
        if(!key_exists($product_id, $this->_discounts)) {
            $this->_discounts[$product_id] = Discount::findActiveByProduct($product_id, Discount::APPLY_TO_PRODUCT);
        }
        return $this->_discounts[$product_id];
    }

    /**
     * Retorno  algun descuento para el customer o producto en el caso de que existan.
     * No tengo en cuenta los descuentos por referenciados.
     *
     * @param array $customerDiscounts
     * @param integer $product_id
     * @return mixed|null
     */
    private function getDiscount($product_id, $customerDiscounts = null, $isPlan = false)
    {
        $discount = null;
        try {
            // Verifico si el customer tiene algun descuento para el plan
            // Solo aplico un descuento, el primero que encuentro
            if ($customerDiscounts) {
                foreach ($customerDiscounts as $key => $value) {
                    if ((( $value->discount->apply_to == Discount::APPLY_TO_CUSTOMER &&
                        $value->discount->product_id == $product_id ) ||
                        ( $value->discount->apply_to == Discount::APPLY_TO_PRODUCT &&
                            $value->discount->product_id == $product_id ) ) || ( $value->discount->value_from == Discount::VALUE_FROM_PLAN && $isPlan)
                    ) {
                        // Cuento los periodos aplicados, si hay menos de la cantidad máxima, lo retorno.
                        $discount = $value->discount;
                        break;
                    }
                }
            }
        }catch(Exception $ex){
        }
        if(is_array($discount) && !empty($discount)){
            $discount = $discount[0];
        }
        return $discount;
    }

    private function createMobilePush(){
        $mobile_push= new MobilePush();
        $mobile_push->title = 'Westnet';
        $mobile_push->content= Config::getValue('invoice_mobile_push_content');
        $mobile_push->status= 'draft';
        $mobile_push->type= 'invoice';

        if (!$mobile_push->save()){
            return null;
        }

        return $mobile_push;
    }

    /**
     * Agrega a la mobile_push recibida el cliente recibido, si tiene una nueva factura y esta cerrada
     * @param $mobile_push
     * @param $customer_id
     * @throws \yii\base\InvalidConfigException
     */
    private function addCustomerToMobilePush($mobile_push, $customer_id){
        $billSearch= new BillSearch(['fromDate' => Yii::$app->formatter->asDate(time(), 'dd-MM-yyyy'), 'customer_id' => $customer_id]);
        $bills = $billSearch->search(['BillSearch' => [
                'fromDate' => Yii::$app->formatter->asDate(time(), 'dd-MM-yyyy'),
                'customer_id' => $customer_id,
                'status' => 'closed'
            ]
        ]);

        if ($mobile_push && count($bills) > 0){
            $mobile_push->addUserApp($customer_id);
        }
    }
}
