<?php

namespace app\modules\sale\models;

use app\components\companies\ActiveRecord;
use app\components\db\ModifierBehavior;
use app\modules\accounting\components\CountableInterface;
use app\modules\automaticdebit\models\AutomaticDebit;
use app\modules\automaticdebit\models\BillHasExportToDebit;
use app\modules\checkout\models\BillHasPayment;
use app\modules\config\models\Config;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\ticket\behaviors\TicketBehavior;
use Yii;
use \app\modules\checkout\models\Payment;
use \app\modules\sale\modules\invoice\components\Invoice;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use app\modules\mailing\components\sender\MailSender;
use app\modules\accounting\components\AccountMovementRelationManager;
use webvimark\modules\UserManagement\models\User;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Da\QrCode\QrCode;
use kartik\mpdf\Pdf;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "bill".
 *
 * @property integer $bill_id
 * @property string $date
 * @property string $time
 * @property integer $timestamp
 * @property integer $number
 * @property string $currency
 * @property double $amount
 * @property integer $customer_id
 * @property string $observation
 * @property string $user_id
 * @property integer $partner_distribution_model_id
 * @property integer $invoice_process_id
 *
 * @property Customer $customer
 * @property BillDetail[] $billDetails
 * @property BillType $billType
 * @property string $ein
 * @property Date $ein_expiration
 * @property User $user
 * @property PartnerDistributionModel $partnerDistributionModel
 */
class Bill extends ActiveRecord implements CountableInterface
{
    const STATUS_DRAFT = 'draft';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CLOSED = 'closed';
    const STATUS_ERROR = 'error';

    public $fillNumber = true;

    public $pointOfSale;

    public $iso_expiration;
    
    /**
     * Determina si se debe registrar fecha de vencimiento para este comprobante
     * @var boolean 
     */
    static $expirable = false;
    
    /**
     * Determina si este comprobante es pagable
     * @var boolean 
     */
    static $payable = true;
    
    /**
     * Determina si el comprobante puede abrirse una vez q esta en estado "closed"
     * @var boolean
     */
    static $openable = false;
    
    /**
     * Determina si el comprobante puede desactivarse al generar un comprobante
     * a partir de el.
     * @var boolean
     */
    static $deactivable = false;
    
    /**
     * Indica si este documento debe ser considerado como un estado final
     * para un workflow dado. En caso de ser false y encontrarse activo, el 
     * workflow se considera como pendiente.
     * https://docs.google.com/document/d/1rf44UE0cUj0rSmS5jy_U40R0VNuouh3RcQR7A3TWrtI/edit#heading=h.8653qyhayrkz
     * @var boolean
     */
    static $endpoint = false;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bill';
    }
    
    /**
     * Instancia un nuevo objeto de acuerdo al tipo. El objeto puede ser:
     *  Order.
     *  Bill.
     * @param array $row
     * @return \app\modules\sale\models\Plan|\app\modules\sale\models\Service|\self
     */
    public static function instantiate($row)
    {
        if($row['class'] !== null) {
            return new $row['class'];
        }else{
            return new self;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /**
         * Agrega un manejador a EVENT_BEFORE_VALIDATE.
         * Antes de validar, valida que el tipo de cliente corresponda al tipo
         * de factura.
         */
        $this->on(self::EVENT_BEFORE_VALIDATE, function(){

            if($this->customer && $this->billType && !$this->customer->checkBillType($this->billType)){
                $this->addError('bill_type_id', Yii::t('app','Current customer requires "{billType}"'
                    , ['billType' => $this->customer->taxCondition->billTypesNames]) );
            }
        });
        
        return array_merge(parent::rules(), [
            [['bill_type_id'], 'required'],
            [['currency'], 'string', 'max' => 45],
            [['observation'], 'string', 'max' => 250],
            [['company_id', 'user_id', 'partner_distribution_model_id'], 'number'],
            [['partnerDistributionModel', 'point_of_sale_id', 'date' , 'number', 'automatically_generated'], 'safe']
        ]);
    }

    public function behaviors()
    {
        return [
            'datestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){
                    return date('Y-m-d');
                }
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('H:i');},
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'account' => [
                'class'=> 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ],
            'modifier' => [
                'class'=> 'app\components\db\ModifierBehavior'
            ],
            'ticket' => [
                'class'=> 'app\modules\ticket\behaviors\TicketBehavior'
            ],
        ];
    }

    /**
     * Inicializa tipo por defecto de factura, tipo por defecto de moneda, y valida estos datos.
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        
        parent::init();
        
        $this->active = true;
        
        //$defaultType = Yii::$app->params['bill_default_type'];
        $defaultCurrency = Yii::$app->params['bill_default_type'];
        
        //Verificacion
        //        if(!BillType::find()->exists() 
        //            || !BillType::find()->where(['bill_type_id'=>$defaultType])->exists()){
        //            
        //                throw new \yii\web\HttpException(500, Yii::t('app','No bill types avaible or bad configuration.'));
        //        }
        
        if(!Currency::find()->exists()
            || !Currency::find()->where(['currency_id'=>$defaultCurrency])->exists()){
            
                throw new \yii\web\HttpException(500, Yii::t('app','No currencies avaible or bad configuration.'));
        
        }
        
        //$this->bill_type_id = $defaultType;
        $this->currency_id = $defaultCurrency;
        
        $this->payed = false;
        
        $this->footprint = uniqid('grp', true);
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bill_id' => Yii::t('app', 'Bill ID'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'number' => Yii::t('app', 'Number'),
            'type' => Yii::t('app', 'Type'),
            'bill_type_id' => Yii::t('app', 'Type'),
            'currency' => Yii::t('app', 'Currency'),
            'currency_id' => Yii::t('app', 'Currency'),
            'amount' => Yii::t('app', 'Amount'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'status' => Yii::t('app', 'Status'),
            'payed' => Yii::t('app', 'Payed'),
            'observation' => Yii::t('app', 'Observation'),
            'taxes' => Yii::t('app', 'Taxes'),
            'expiration' => Yii::t('app', 'Expiration date'),
            'payment_methods' => Yii::t('app', 'Payment Methods'),
            'expired' => Yii::t('app', 'Expired'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
            'partner_distribution_model_id' => Yii::t('partner', 'Partner Distribution Model'),
            'point_of_sale_id' => Yii::t('app', 'Point of Sale')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillDetails()
    {
        return $this->hasMany(BillDetail::className(), ['bill_id' => 'bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillHasPayments()
    {
        return $this->hasMany(BillHasPayment::className(), ['bill_id' => 'bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillType(){

        return $this->hasOne(BillType::className(), ['bill_type_id' => 'bill_type_id']);

    }

    /**
     * Devuelve nombre del tipo de comprobante
     * @return string
     */
    public function getTypeName(){
        return $this->billType ? $this->billType->name : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency(){

        return $this->hasOne(Currency::className(), ['currency_id' => 'currency_id']);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceClass(){

        return $this->billType->invoiceClass;

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceProcess()
    {
        return $this->hasOne(InvoiceProcess::class, ['invoice_process_id' => 'invoice_process_id']);
    }

    /**
     * @param bool $withTax
     * @param bool $perDetail
     * @return float|int
     * variable perDetail indica si la funcion devuelve el descuento que seria aplicado a cada item o el descuento total
     */
    private function getFixedDiscountPerDetail($withTax = true, $perDetail = true)
    {
        $discount = 0;
        $cantDiscount = 0;
        $cantItems = 0;
        foreach ($this->billDetails as $detail){
            if($detail->discount) {
                if( $detail->discount->value_from == Discount::VALUE_FROM_TOTAL &&
                    $detail->discount->type  == Discount::TYPE_FIXED){

                    // Le descuento el iva
                    if($detail->product) {
                        $taxes = 0;
                        $taxRates = $detail->product->taxRates;
                        foreach($taxRates as $rate){
                            $taxes += ( $detail->unit_net_discount / (1+($withTax ? $rate->pct : 0 )));
                        }

                        $discount += $taxes;
                    } else {
                        $taxRate = TaxRate::findOne(['code' => Config::getValue('default_tax_rate_code')]);
                        if($taxRate) {
                            $discount +=( $detail->unit_net_discount / (1 + ($withTax ? $taxRate->pct : 0 )));
                        } else {
                            $discount += $detail->unit_net_discount;
                        }
                    }

                    $cantDiscount++;
                }
            }
            // had to separate the condition for having a discount because some details where both ITEM and had DISCOUNT id associated
            if ($detail->product) { // if the detail has a product_id, then its an ITEM.
                $cantItems++;
            }
        }   

        if($perDetail){
            // $cantItems = ($cantItems == 0) ? 1 : $cantItems;
            return ($cantDiscount ? ($discount / $cantItems) : 0);
        }
        return ($cantDiscount ? $discount : 0);
    }

    private function getPercentageDiscountPerDetail($withTax=true)
    {
        $discount = 0;
        $cantDiscount = 0;
        foreach ($this->billDetails as $detail){
            if($detail->discount) {
                if( $detail->discount->value_from == Discount::VALUE_FROM_TOTAL &&
                    $detail->discount->type  == Discount::TYPE_PERCENTAGE){

                    // Le descuento el iva
                    if($detail->product) {
                        $taxes = 0;
                        $taxRates = $detail->product->taxRates;
                        foreach($taxRates as $rate){
                            $taxes +=  ( $detail->discount->value / (1+($withTax ? $rate->pct : 0 )));
                        }

                        $discount +=  $taxes;
                    } else {
                        $taxRate = TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code')]);
                        if($taxRate) {
                            $discount += ( $detail->discount->value / (1+($withTax ? $taxRate->pct : 0)));
                        } else {
                            $discount += $detail->discount->value;
                        }
                    }
                    $cantDiscount++;
                } else if($detail->discount->type  == Discount::TYPE_PERCENTAGE && $detail->discount->value_from == 'plan') {
                    if($detail->product) {
                        if($detail->product->type == 'plan') {
                            $taxes = 0;
                            $taxRates = $detail->product->taxRates;
                            foreach($taxRates as $rate){
                                $taxes +=  ( $detail->discount->value / (1+($withTax ? $rate->pct : 0 )));
                            }

                            $discount +=  $taxes;
                        }
                    }
                }
            }
        }

        return ($cantDiscount ? (100-$discount)/100 : 1);
    }

    /**
     * Devuelve el monto total de la factura
     * @return float
     */
    public function calculateAmount()
    {
        $discountFixedDetail = $this->getFixedDiscountPerDetail(true, false);
        $discountPercentDetail = $this->getPercentageDiscountPerDetail(true);

        $amount = 0.0;

        foreach ($this->billDetails as $detail) {
            //Verifico que no sea un producto en linea, si es un descuento aplicado al producto (como el recomendado), debo poner el importe de la line_subtotal
            $is_inline_discount = $detail->discount_id && (!$detail->product_id) ? true : false;
            // Se suma el line_subtotal solo si no es un descuento, ya que si no va a sumar lo que se puso en el descuento que viene en positivo
            $amount += (float)($is_inline_discount ? 0 : $detail->line_subtotal);//($detail->line_subtotal - $discountFixedDetail) * $discountPercentDetail );
        }
        $amount = ($amount - $discountFixedDetail) * $discountPercentDetail;
        $amount = ($amount < 0 ? 0 : $amount);

        return round($amount,2);
    }

    /**
     * Devuelve el monto total con iva de la factura
     * @return float
     */
    public function calculateTotal()
    {
        $discountFixedDetail = $this->getFixedDiscountPerDetail(false, false);
        $discountPercentDetail = $this->getPercentageDiscountPerDetail(false);
        $total = 0.0;
        foreach ($this->billDetails as $detail){
            //La segunda parte de esta condicion es para resolver un problema con los detalles migrados del sistema anterior
            if(($discountFixedDetail>0 || $discountPercentDetail) && !($detail->line_subtotal == 0 && $detail->line_total > 0)) {
                $pct = ($detail->line_subtotal ? $detail->line_total / $detail->line_subtotal : 0 );
            } else {
                $taxRate = TaxRate::findOne(['code'=>Config::getValue('default_tax_rate_code')]);
                $pct = $taxRate->pct;
            }
            $total += (float)(($detail->discount_id && $detail->unit_net_price == 0) ? 0 : $detail->line_subtotal) * round($pct,2);
        }

        $total = ($total - $discountFixedDetail ) * $discountPercentDetail;
        $total = ($total < 0 ? 0 : $total);

        return round($total,2);
    }

    /**
     * Devuelve el monto total con iva de la factura
     * @return float
     */
    public function calculateTaxes()
    {

        return abs(round( $this->calculateTotal() - $this->calculateAmount(), 2));

    }

    public function totalDiscount()
    {
        $total = 0.0;

        foreach ($this->billDetails as $detail) {
            $total += (float)$detail->getTotalDiscount();
        }

        return round($total,2);

    }

    public function totalDiscountWithTaxes()
    {
        $total = 0.0;

        foreach ($this->billDetails as $detail) {
            $total += (float)$detail->getTotalDiscount(true);
        }

        return round($total,2);
    }


    /**
     * Permite agregar un detalle a una factura. El detalle solo puede
     * agregarse a una factura con estado draft.
     * @param array $detail :
     * array(
     *  'description' => 'String. Requerido. Descripcion textual del item aqui',
     *  'amount' => Float. Requerido. Monto positivo o negativo,
     *  'model' => 'String. Nombre del modelo, por ejemplo "Ad',
     *  'model_id' => Int. Id del modelo de referencia,
     *  'IVA' => Float. Porcentaje flotante (0.0 - 100.0)
     * )
     * @return boolean
     * @throws \yii\web\HttpException
     */
    public function addDetail($data)
    {

        if($this->isNewRecord) return false;
        if($this->status != 'draft') return false;

        //Completamos los datos con los valores por defecto
        $detail = array_merge(
            array(
                'product_id' => null,
                'qty' => 1,
                'type' => 'product',
                'unit_net_price' => 0,
                'unit_final_price' => 0
            ),
            $data
        );
        //Validamos que se haya especificado la descripcion y el monto
        if(!array_key_exists('concept', $detail) || !array_key_exists('unit_final_price', $detail))
            throw new \yii\web\HttpException(500, 'Ha ocurrido un error al detallar la factura.');

        //En caso de corresponder a un producto, buscamos si ya hay un detalle del mismo e incrementamos qty
        if($detail['product_id']){
            $billDetail = $this->getDetail($detail['product_id']);
        }

        if(empty($billDetail)){

            //Generamos el detalle
            $billDetail = new BillDetail();
            Yii::info($detail, 'Deuda');

            $billDetail->setAttributes($detail);
            Yii::info($billDetail, 'Deuda');


            $this->link('billDetails',$billDetail);

        }else{
            $billDetail->qty += $data['qty'];
            $billDetail->validate();
            $billDetail->save();

        }

        //Datos de detalle
        return $billDetail;

    }

    public function updateAmounts(){

        //Calculos:
        $this->amount = $this->calculateAmount();
        $this->total = $this->calculateTotal();
        $this->taxes = $this->calculateTaxes();
        $this->updateAttributes(['amount','total','taxes']);

    }

    /**
     * Pasa una factura a estado "complete d". Calcula el monto total de la
     * factura y la guarda, ademas de actualizar la fecha y hora.
     * TODO: generar numero de factura
     * @return boolean
     */
    public function complete()
    {
        if($this->status != 'draft') return false;

        if($this->status == 'completed') return true;

        //Calculos:
        $this->amount = $this->calculateAmount();
        $this->total = $this->calculateTotal();
        $this->taxes = $this->calculateTaxes();

        $this->status = 'completed';

        // Si es nota de credito
        /*if ($this->billType->multiplier < 0) {
            $this->payed = true;
        }*/

        //Fecha y hora
        //Conservar hora anterior en caso de que haya sido seteada de forma manual
        if(!$this->date || $this->date == '0000-00-00'){
            $date = new \DateTime();
            $this->date = $date->format('Y-m-d');
            $this->time = $date->format('H:i');
        }

        if($this->save()){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Cierra la factura. Antes de cerrarla verifica que este completada.
     * En caso de no estar completada, se completa y luego se cierra.
     * Al finalizar el escenario es 'closed'
     * @return boolean
     */
    public function close()
    {	
        \Yii::info("2) Entre en modules/sale/models/bill/function_close", 'duplicados-afip');
        $transaction = $this->db->beginTransaction();

        try{
            if($this->status == null){
                $this->status = 'draft';
            }

            if($this->number){
                $this->updateAttributes(['number' => $this->number]);
            }

            //Si el estado es 'draft' primero debemos completar la factura
            if($this->status == 'draft'){
                if(!$this->complete()){
                    return false;
                }
            }

            if($this->status == 'completed'){

                \app\modules\sale\components\BillExpert::manageStock($this);
                
                //Factura electronica
                $succeed = $this->invoice();

                if($succeed){
                    $transaction->commit();
                    return true;
                }else {
                    $transaction->rollback();
                    return false;
                }
            }
        }  catch (\Exception $e){
            echo 'ERROR______________ '.$e->getTraceAsString()."\n";
            \Yii::info('ERROR ________________ ' .$e->getTraceAsString(), 'facturacion-cerrado');
            $transaction->rollback();
        }
        
        //En caso de llegar a este punto, retornamos false
        $transaction->rollback();
        //se agrega cambio de estado para conservar que hubo un error sin importar de donde se intente cerrar la factura (postROLLBACK)
        // $keys = Bill::getConcatedKeyErrors($this);
        $this->updateAttributes(['had_error' => true]);
        return false;

    }

    /**
     * Genera la factura electronica en base a la clase del tipo de factura.
     * IMPORTANTE No quitar los \Yii::info con categoria facturación, ya que están para realizar un log de los errores que se puedan presentar
     * los errores se pueden ver desde la carpeta runtime/logs/app_facturacion.log
     * @return bool
     */
    public function invoice()
    {
        $had_error = false;
        $invoiceClass = $this->billType->invoiceClass;
        $retValue = false;
        //retValue is the return Boolean to check if invoice was OK
        if ($this->getPointOfSale()->electronic_billing == 1) {
            if ($invoiceClass && class_exists($invoiceClass->class)) {
                //Si electronic_billing del punto de venta esta en 1 significa que la factura electronica debe realizarse.
                try {
		            \Yii::info("3) Entre en modules/sale/models/bill/function_invoice", 'duplicados-afip');

                    $invoice = Invoice::getInstance();
                    $result = $invoice->invoice($this);
                    // Si no registra contra ws se vuelve a draft
                    $backToDraft = true;

                    if ($result['status'] == 'success') {
                        // Si esta aprobado
                        if ($result['result']['resultado'] == 'A') {
                            $this->ein = $result['result']['cae'];
                            $this->ein_expiration = $result['result']["vencimiento"];
                            $msg = Yii::t('app', 'Invoice successfully created.');
                            $this->status = 'closed';
                            $backToDraft = false;
                            if(!Yii::$app instanceof Yii\console\Application) {
                                Yii::$app->session->addFlash('success', Yii::t('app', 'Invoice successfully created.'));
                            }
                            $retValue = true;
                        } else {
                            //The else cases are errors. for example 'R' (which can be successful but still not a valid invoice)
                            //...
                            $retValue = false;
		                    $had_error = true;
                            $backToDraft = true; // send it to draft (with status=error)
                            $this->addErrorToCacheOrSession('An error occurred while the Invoice is processed.'. ' - Bill_id: '.$this->bill_id);
                            \Yii::info(Yii::t('app', 'An error occurred while the Invoice is processed.') . ' - Bill_id: '. $this->bill_id, 'facturacion');
                            if(!Yii::$app instanceof Yii\console\Application) {
                                Yii::$app->session->addFlash('error', Yii::t('app', 'An error occurred while the Invoice is processed.'));
                            }
                        }
                    } else {
                        $backToDraft = true;
                    }
                    $this->save();
                    if ($backToDraft) {
                        $this->payed = false;
                        $this->status = 'draft';

                        $payments = BillHasPayment::find()
                                        ->where(['bill_id' => $this->bill_id])->all();

                        $delete = [];
                        foreach ($payments as $pay) {
                            if (BillHasPayment::find()
                                            ->where(['payment_id' => $pay->payment_id])
                                            ->andWhere('bill_id<>' . $this->bill_id)->count()
                            ) {
                                $delete[] = $pay->payment_id;
                            }
                        }
                        BillHasPayment::deleteAll(['payment_id' => $delete]);
                        Payment::deleteAll(['payment_id' => $delete]);

                        $this->save();
                    }

                    foreach ($result['errors'] as $msg) {
                        $this->addErrorToCacheOrSession('Codigo: ' . $msg['code'] . ' - ' . $msg['message'].' - Bill_id: '.$this->bill_id, 'error');
                        \Yii::info('Codigo: ' . $msg['code'] . ' - ' . $msg['message'].' - Bill_id: '.$this->bill_id, 'facturacion');
                    }
                    foreach ($result['observations'] as $msg) {
                        $this->addErrorToCacheOrSession('Codigo: ' . $msg['code'] . ' - ' . $msg['message'].' - Bill_id: '.$this->bill_id);
                        \Yii::info('Codigo: ' . $msg['code'] . ' - ' . $msg['message'].' - Bill_id: '.$this->bill_id, 'facturacion');
                    }
 
                    return ($retValue && empty($result['errors']) && !$had_error);
                } catch (\Exception $ex) {
                    $had_error = true;
                    \Yii::info($ex, 'facturacion');
                    $this->addErrorToCacheOrSession('Codigo: ' . $msg['code'] . ' - ' . $msg['message'].' - Bill_id: '.$this->bill_id);
                    return false;
                }
            } else {
                //Si un tipo no tiene asociado factura electronica, la actualizacion de estado se debe realizar de todas formas
                $msg = Yii::t('app', 'Invoice successfully created.');
                if (!Yii::$app instanceof Yii\console\Application) {
                    Yii::$app->session->addFlash('success', Yii::t('app', 'Invoice successfully created.'));
                }
                $this->updateAttributes(['status' => 'closed']);

                //Se llama al behavior para cerrar los tickets que tenga el cliente de cobranza
                $this->trigger('EVENT_CLOSE_TICKETS');

                //Agrega el numero de comprobante
                if ($this->fillNumber) {
                    $this->fillNumber();
                }
            }
        } else {
            //Si un tipo no tiene asociado factura electronica (por el punto de venta), la actualizacion de estado se debe realizar de todas formas
            //Si es un registro nuevo significa que viene desde la facturación por lotes.
            if($this->isNewRecord){
                $this->number = $this->fillNumber(true);
            } else {
                if (!Yii::$app instanceof Yii\console\Application) {
                    Yii::$app->session->addFlash('success', Yii::t('app', 'Invoice successfully created.'));
                }
                $this->updateAttributes(['status' => 'closed']);

                //Se llama al behavior para cerrar los tickets que tenga el cliente de cobranza
                $this->trigger('EVENT_CLOSE_TICKETS');
            }
        }

        return true;
    }

    /**
     * Examina si es una instacia de consola o no, y agrega los mensajes de error a cache o a session según corresponda.
     */
    public function addErrorToCacheOrSession($error, $key = 'error'){
        // todo: isnt $key = null causing errors??
        if(Yii::$app instanceof Yii\console\Application) {
            $old_errors = Yii::$app->cache->get('_invoice_close_errors');
            Yii::$app->cache->set('_invoice_close_errors', array_merge($old_errors, [$error]), 300); // this casts any string into an array to store in memory
        } else {
            Yii::$app->session->addFlash($key, $error);
        }
    }

    /**
     * Si el producto solicitado ya ha sido agregado a la factura, devuelve
     * el detalle relacionado
     * @param type $product_id
     * @return BillDetail
     */
    private function getDetail($product_id){

        return $this->getBillDetails()->where(['product_id'=>$product_id])->one();

    }

    /**
     * Genera un color para utilizar en los graficos
     * @return string
     */
    public function getRgb(){
        $hash = md5('color' . $this->date);
        return
            hexdec(substr($hash, 0, 2)).','.    // r
            hexdec(substr($hash, 2, 2)).','.    // g
            hexdec(substr($hash, 4, 2))         // b
        ;
    }

    /**
     * Campos que deben ser retornados por toArray()
     * @return type
     */
    public function fields() {

        return [
            'bill_id',
            'date',
            'time',
            'billType',
            'amount',
            'total',
            'company_id',
            'customer',
            'bill_type_id',
            'observation',
            'billDetails'=>function($model, $field){
                return $model->billDetails;
            },
            'expiration'=>function($model, $field){
                return $model->iso_expiration;
            }
        ];

    }

    /**
     * Verifica el monto pagado de la factura, y actualiza el estado de la misma
     * si fue pagada completamente.
     * @param type $payment
     */
    public function checkPayment(){

        //Actualizamos desde la base de datos para que recupere todos los modelos seteados
        $this->refresh();
        $payedAmount = $this->getPayedAmount();

        $total = $this->calculateTotal();

        //Verificamos q el monto pagado supere la tolerancia
        if($total - $payedAmount <= $total * Yii::$app->params['payment_tolerance']){
            $this->updateAttributes(['payed'=>true]);
            $this->close();
        }

    }

    /**
     * De
     * @return type
     */
    public function getPayedAmount($includeDraft=false)
    {

        $payedAmount = 0.0;
        foreach($this->billHasPayments as $payment){
            if ( ($includeDraft && $payment->payment->status=='draft') || ($payment->payment->status!='draft')) {
                $payedAmount += $payment->amount;
            }
        }

        return $payedAmount;

    }

    /**
     *
     * @return type
     */
    public function getIsPayed(){

        //Verificamos el estado de pago de la factura, para lo cual debemos actualizar los datos desde
        $this->refresh();
        return (boolean)$this->payed;

    }

    /**
     * Devuelve el importe restante de la factura. Si aun no hay ningun pago,
     * devolvera el importe total de la factura.
     * @return real
     */
    public function getDebt(){

        $payedAmount = $this->getPayedAmount(true);

        $total = $this->calculateTotal();

        if(abs($total - $payedAmount) > $total * Yii::$app->params['payment_tolerance']){
            return $total - $payedAmount;
        }else{
            return 0.0;
        }

    }

    public function getDeletable(){

        // No se permite eliminar si ya esta cerrada o si es electronica y tiene ein
        if ($this->status == 'closed' || ($this->billType->invoiceClass !== null && ( !is_null($this->ein) && !empty($this->ein) ) )) {
            return false;
        }

        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        return true;
    }


    public function beforeDelete() {
        $this->unLinkAll('billDetails', true);
        BillHasPayment::deleteAll(['bill_id' => $this->bill_id]);
        AccountMovementRelationManager::delete($this);
        parent::beforeDelete();
        return true;
    }

    /**
     * Abre una factura: cambia su estado de completed a draft
     * @return boolean
     */
    public function open(){

        if($this->status != 'completed'){
            return false;
        }

        return (boolean) $this->updateAttributes(['status' => 'draft']);

    }

    public function hasCompletedPayment()
    {
        $ok = false;
        foreach($this->getBillHasPayments()->all() as $bhp ){
            $ok = ($bhp->payment->status!="draft");
            if (!$ok) {
                break;
            }
        }

        return $ok;
    }

    /**
     * Retorna un array con todos los impuestos aplicados a los items
     * Analiza si el detalle tiene un producto, es un descuento o un detalle manual, y crea el array de impuestos según el caso.
     * Tiene en cuenta descuentos aplicados.
     * @return array
     */
    public function getTaxesApplied()
    {
        $taxesApplied = [];
        $discount = $this->getFixedDiscountPerDetail() ;
        $fixedDiscount = $discount ? $discount : 0;

        foreach ($this->billDetails as $detail) {
            if (isset($detail->product)) {

                $this->getProductTaxes($taxesApplied, $detail, $fixedDiscount);

            } else {
                if($detail->unit_net_discount != 0 ) {
                    if(!$fixedDiscount) {
                        $this->getDiscountTaxes($taxesApplied, $detail, $fixedDiscount);
                    }

                } elseif($detail->unit_net_price != 0) {

                    $this->getManualDetailTaxes($taxesApplied, $detail, $fixedDiscount);
                }
            }
        }
        return $taxesApplied;
    }

    private function getProductTaxes(&$taxesApplied, $detail, $fixedDiscount)
    {
        foreach ($detail->product->taxRates as $taxRate) {
            $amount = array_key_exists($taxRate->tax_rate_id, $taxesApplied) !== false ? $taxesApplied[$taxRate->tax_rate_id]['amount'] : 0;
            $base = array_key_exists($taxRate->tax_rate_id, $taxesApplied) !== false ? $taxesApplied[$taxRate->tax_rate_id]['base'] : 0;

            $taxesApplied[$taxRate->tax_rate_id] = [
                'tax_id' => $taxRate->tax_rate_id,
                'amount' => round($amount + ($detail->product->calculateTaxAmount('iva', ($detail->line_subtotal - $fixedDiscount) )), 2),
                'base' => round($base + ($detail->line_subtotal - $fixedDiscount) , 2)
            ];
        }
    }

    private function getDiscountTaxes(&$taxesApplied, $detail, $fixedDiscount) {
        $tax_rate = TaxRate::findTaxRateByPct(0);
        $amount = array_key_exists($tax_rate->tax_rate_id, $taxesApplied) !== false ? $taxesApplied[$tax_rate->tax_rate_id]['amount'] : 0;
        $base = array_key_exists($tax_rate->tax_rate_id, $taxesApplied) !== false ? $taxesApplied[$tax_rate->tax_rate_id]['base'] : 0;

        $taxesApplied[$tax_rate->tax_rate_id] = [
            'tax_id' => $tax_rate->tax_rate_id,
            'amount' => round($amount + ($detail->line_total - $detail->subtotal), 2),
            'base' => round($base + $detail->line_subtotal, 2)
        ];
    }

    private function getManualDetailTaxes(&$taxesApplied, $detail, $fixedDiscount)
    {
        $pct = abs(1 - ($detail->unit_final_price / $detail->unit_net_price));
        $tax_rate = TaxRate::findTaxRateByPct($pct);

        if(!$tax_rate) {
            $code = Config::getValue('default_tax_rate_code');
            $tax_rate = TaxRate::findOne(['code' => $code]);
        }

        $amount = array_key_exists($tax_rate->tax_rate_id, $taxesApplied) !== false ? $taxesApplied[$tax_rate->tax_rate_id]['amount'] : 0;
        $base = array_key_exists($tax_rate->tax_rate_id, $taxesApplied) !== false ? $taxesApplied[$tax_rate->tax_rate_id]['base'] : 0;

        $taxesApplied[$tax_rate->tax_rate_id] = [
            'tax_id' => $tax_rate->tax_rate_id,
            'amount' => round($amount + (($detail->line_subtotal - $fixedDiscount)*$pct), 2),
            'base' => round($base + $detail->line_subtotal - $fixedDiscount , 2)
        ];
    }

    /**
     * Retorna la configuracion usadas para la registracion de movimientos
     * En principio se devuelven todos los impuestos, el total de bill y el resto.
     *
     * @return array
     */
    public function getConfig()
    {
        // Traigo los impuestos y sus porcentajes, para distirlos uno de otro.
        $query = TaxRate::find();
        $query->select(['tax_rate.tax_rate_id', "concat(tax.name, ' ', (tax_rate.pct * 100), '%' ) as name", 'tax_rate.tax_id'])
            ->leftJoin('tax', 'tax_rate.tax_id = tax.tax_id');
        $taxes = ArrayHelper::map($query->asArray()->all(), 'tax_rate_id', 'name');

        $taxes['total'] = 'Total';
        $taxes['rest'] = 'Resto';
        return $taxes;
    }

    /**
     * Retorna los valores para cada item de la configuracion.
     *
     * @return array
     */
    public function getAmounts()
    {
        $taxes = $this->getTaxesApplied();

        $rest = 0;
        foreach($taxes as $tax){
            $rest += ($tax['amount'] > 0 ? $tax['amount'] : 0);
        }

        return $taxes + [
            'total' => $this->calculateTotal(),
            'rest'  => $this->calculateTotal() - $rest,
        ];
    }

    /**
     * Devuelve el pto de venta por defecto de la empresa
     */
    public function getPointOfSale()
    {
        if($this->point_of_sale_id){
           return $this->hasOne(PointOfSale::className(), ['point_of_sale_id' => 'point_of_sale_id'])->one();
        } else {
           return $this->company->defaultPointOfSale;
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();

            if($this->status == 'draft'){
                if ($this->number) {
                    if(!$this->point_of_sale_id){
                        $this->point_of_sale_id = $this->company->getDefaultPointOfSale();
                    }
                    $query = Bill::find()->where(['number' => $this->number, 'point_of_sale_id' => $this->point_of_sale_id, 'bill_type_id' => $this->bill_type_id]);
                    $existing_bill = $this->bill_id ? $query->andWhere(['<>', 'bill_id', $this->bill_id])->one() : $query->one();
                    if ($existing_bill) {
                        if(!Yii::$app instanceof Yii\console\Application) {
                            Yii::$app->session->setFlash('danger', Yii::t('app', 'The bill number already exists') . ': '. $this->number);
                        }
                        return false;
                    } else {
                        $this->updateAttributes(['number' => $this->number]);
                    }
                }
            }
            if ($this->status != null && $this->status != 'draft') {
                //Validaciópn de numero de factura
                if ($this->number) {
                    $query = Bill::find()->where(['number' => $this->number, 'point_of_sale_id' => $this->pointOfSale, 'bill_type_id' => $this->bill_type_id, 'status' => Bill::STATUS_CLOSED, 'company_id' => $this->company_id]);
                    $existing_bill = $this->bill_id ? $query->andWhere(['<>', 'bill_id', $this->bill_id])->one() : $query->one();
                    if ($existing_bill) {
                        //Yii::$app->session->setFlash('danger', Yii::t('app', 'The bill number already exists'));
                        //Para que el error sea agregado realmente, se debe llamar a la funcion fillErrors de esta clase.
                        $this->addError('number_duplicated', '');
                        return false;
                    }
                } else {
                    //Si el punto de venta indica emitir factura electronica se vacia el numero, ya que se autocompletará luego
                    if ($this->point_of_sale_id) {
                        if ($this->getPointOfSale()->electronic_billing != 1) {
                            //Si no tiene numero y el punto de venta es el por defecto, significa que el comprobante se genero automaticamente
                            // (facturación por lotes x ej.) y se autocompletara el numero.
                            if ((!$this->number) && ($this->point_of_sale_id == $this->company->defaultPointOfSale->point_of_sale_id)) {
                                $this->number = $this->fillNumber(true);
                                return true;
                            }
                            //Si no tiene numero y el punto de venta no es por defecto, significa que se esta cargando un comprobante manual,
                            //por lo que el numero es requerido.
                            if ((!$this->number) && ($this->point_of_sale_id != $this->company->defaultPointOfSale->point_of_sale_id)) {
                                //Para que el error sea agregado realmente, se debe llamar a la funcion fillErrors de esta clase.
                                $this->addError('number_missing', '');
                                return false;
                            }

                            if (!$this->number) {
                                if(!Yii::$app instanceof Yii\console\Application) {
                                    Yii::$app->session->addFlash('warning', 'falta numero');
                                }
                                return false;
                            }
                        }
                    }
                }
            }
            // Verifico la fecha, que no puede ser mayor a 5 dias para atras, en el caso de que asi sea
            //  pongo la fecha de hoy
            if ($this->point_of_sale_id){
                if($this->getPointOfSale()->electronic_billing ) {
                    if($this->date) {
                        if ($this->date != '<span class="not-set">(no definido)</span>') {
                            $date = new \DateTime($this->date);
                            if ((new \DateTime('now'))->diff($date)->days > 5) {
                                $this->date = (new \DateTime('now'))->format('Y-m-d');
                            }
                        } else {
                            $this->date = (new \DateTime('now'))->format('Y-m-d');
                        }
                    } else {
                        $this->date = (new \DateTime('now'))->format('Y-m-d');
                    }
                }
            }

            //Validacion pto de venta
            if (!$this->company->defaultPointOfSale) {
                if(!Yii::$app instanceof Yii\console\Application) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'Check company configuration. Point of sale missing.'));
                }
                return false;
            }

            if($this->company) {
                if($this->company->partner_distribution_model_id) {
                    $this->partner_distribution_model_id = $this->company->partner_distribution_model_id;
                }
            }

            //Validacion cliente <-> tipo de comprobante
            if($this->customer){
                if(!$this->checkBillType()){
                    if(!Yii::$app instanceof Yii\console\Application) {
                        Yii::$app->session->setFlash('danger', Yii::t('app', 'Current customer could not be billed with selected company.'));
                    }

                    $this->addError('bill_type_id', Yii::t('app','Current customer requires "{billType}"'
                    , ['billType' => $this->customer->taxCondition->billTypesNames]) );

                    return false;
                }

            }
            
            if(!$this->company->getBillTypes()->where(['bill_type.bill_type_id' => $this->billType->bill_type_id])->exists()){

                $this->bill_type_id = $this->company->defaultBillType->bill_type_id;
            }

            $this->class = $this->billType->class;

            return true;
        }else{
            return false;
        }
    }


    public function fillNumber($with_return = false)
    {
        if ($this->billType && !$this->billType->invoiceClass) {
            if ($this->number) {
                $bill = Bill::find()->where(['number' => $this->number])->exists();
                if($bill && !Yii::$app instanceof Yii\console\Application){
                    Yii::$app->session->addFlash('error', Yii::t('app', 'An error occurred while the Invoice is processed.'));
                }
            } else {
                $lastNumber = Bill::find()->where([
                            'bill_type_id' => $this->bill_type_id,
                            'status' => 'closed',
                            'company_id' => $this->company_id
                        ])
                    ->orderBy(['number' => SORT_DESC])
                    ->limit(1)->one()
                ;
                if($with_return){
                    return (int) $lastNumber->number + 1;
                } else {
                    $this->updateAttributes(['number' => (int) $lastNumber->number + 1]);
                }
            }
        }
    }

    /**
     * Agrega el cliente y realiza validaciones de tipo de comprobante.
     * Si es necesario, cambia la empresa actual a la empresa del cliente.
     * @param type $customer
     */
    public function setCustomer($customer)
    {
        //Si se debe forzar la utilizacion de la empresa asignada al cliente:
        if($customer->company && \app\modules\config\models\Config::getValue('force_customer_company')){
            $this->company_id = $customer->company_id;
            //Para evitar que el punto de venta no se actualice cuando se cambia la empresa.
            $this->point_of_sale_id = $customer->company->getDefaultPointOfSale() ? $customer->company->getDefaultPointOfSale()->point_of_sale_id : '';
        }

        $defaultBillType = $customer->defaultBillType;

        //        var_dump($this->billType);
        //        echo '<br>';
        //        var_dump($customer->checkBillType($this->billType));
        //        die;


        //Si el tipo de comprobante actual no puede ser aplicado al cliente
        if($defaultBillType && !$customer->checkBillType($this->billType)){
            $this->bill_type_id =  $defaultBillType->bill_type_id;

            //Si el tioo de factura del cliente no es soportado por la empresa seleccionada
            if(!$this->company->checkBillType($defaultBillType)){
                if($customer->company){
                    $this->company_id = $customer->company_id;
                    //Para evitar que el punto de venta no se actualice cuando se cambia la empresa.
                    $this->point_of_sale_id = $customer->company->getDefaultPointOfSale() ? $customer->company->getDefaultPointOfSale()->point_of_sale_id : '';
                }

            }else{
                $this->addError('customer_id', Yii::t('app', 'This customer can not be billed with this company or bill type.'));
            }
        }
        $this->save();
        $this->link('customer', $customer);

    }

    /**
     * Valida el tipo de factura de acuerdo al tipo de empresa y al tipo de cliente.
     * Ademas intenta modificar el tipo de factura de acuerdo al tipo de cliente y
     * a la empresa asignada al mismo.
     */
    private function checkBillType()
    {

        if(!$this->customer){
            return true;
        }

        if($this->customer->checkBillType($this->billType) && $this->company->checkBillType($this->billType)){
            return true;
        }

        $company_bills = [];
        foreach($this->company->billTypes as $type){
            $company_bills[] = $type->bill_type_id;
        }

        $billType = $this->customer->taxCondition->getBillTypes()->where(['bill_type_id'=>$company_bills])->one();

        if($billType){
            $this->bill_type_id = $billType->bill_type_id;

            if(Yii::$app instanceof Application)  {
                Yii::$app->session->setFlash('danger', Yii::t('app','Current customer requires "{billType}". Bill type changed.'
                    , ['billType' => $this->customer->taxCondition->billTypesNames]));
            }

            return true;
        }else{
            return false;
        }

    }

    public function getIsEditable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        if ($this->status == 'draft' || $this->status == 'pending' && User::hasPermission('user-can-update-pending-order', true)) {
            return true;
        }

        return false;
    }

    /**
     * Es recomendable llamar a esta funcion dentro de una transaccion
     */
    public function deactivate($save = true)
    {

        $this->active = 0;

        $details = $this->getBillDetails()->select(['bill_detail_id'])->asArray()->all();

        $ids = [];
        foreach($details as $d){
            $ids[] = (int)$d['bill_detail_id'];
        }

        $qty = StockMovement::updateAll(['active' => 0], ['bill_detail_id' => $ids]);

        if($save){
            return $this->save(false, ['active']);
        }

        return true;
    }

    /**
     * Se puede abrir el comprobante?
     * @return boolean
     */
    public function isOpenable()
    {

        $openable = new \ReflectionProperty($this->billType->class, 'openable');

        if($this->active == false || $this->status == 'draft'){
            return false;
        }

        if(($this->status === 'completed' || $openable->getValue()) && !$this->getBillHasPayments()->exists() &&  is_null($this->ein)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Se informa al objeto cdo el tipo cambia.
     * Las crias pueden sobreescribir para hacer algo.
     */
    public function onTypeChange()
    {
        //nothing here
    }

    /**
     *
     * @return type
     */
    public function getUser()
    {
        return $this->hasOne(\webvimark\modules\UserManagement\models\User::className(), ['id' => 'user_id']);
    }

    /**
     *
     * @return type
     */
    public function getAuthor()
    {
        return $this->hasOne(\webvimark\modules\UserManagement\models\User::className(), ['id' => 'author_id']);
    }

    public function afterFind() {
        parent::afterFind();
        $this->iso_expiration = $this->expiration;
    }

    /**
     * Mostrar la columna con checkbox para seleccionar que facturar?
     * @return boolean
     */
    public function allowInvoiceSelection(){
        return false;
    }

    /**
     * Mostrar la columna de checkbox para seleccionar que detalles deben ser
     * usados para generar el comprobante seleccionado? Solo funciona para
     * comprobantes distintos de Factura (bills\Bill)
     * @return boolean
     */
    public function allowDetailSelection(){
        return false;
    }

    public function canBeClosed()
    {
        if(\webvimark\modules\UserManagement\models\User::canRoute('/sale/bill/close') &&
            $this->status != 'pending' &&
            $this->status != 'closed'){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Permite recuperar el total de un grupo de comprobantes, por ejemplo, si
     * se genero una factura de 1000 y luego una nota de credito de 200, este
     * metodo devolvera 800
     * @return type
     */
    public function calculateGroupTotal()
    {
        if($this->footprint){
            return self::find()->joinWith('billType')->where(['footprint'=>$this->footprint, 'active' => 1, 'status' => 'closed'])->sum('total*bill_type.multiplier');
        }else{
            return $this->total;
        }
    }

    public function getMaxBillDate(){
        $date = Bill::find()->max('date');
        return $date;
    }

    /*
     * Determina si el numero de factura debe ser ingresado manualmente
     */

    public function isNumberAutomaticalyGenerated()
    {
        $is_number_manual = 1;
        $point_of_sale = $this->getPointOfSale();
        if ($point_of_sale->point_of_sale_id) {
            if ($this->point_of_sale_id == $this->company->getDefaultPointOfSale()->point_of_sale_id) {
                $is_number_automatic = 1;
            } else {
                if ($point_of_sale->electronic_billing == 0) {
                    $is_number_automatic = 0;
                } else {
                    $is_number_automatic = 1;
                }
            }
        }
        return $is_number_automatic;
    }

    public static function fillErrors($model, $errorMessageKeys)
    {
        $errorMessages = [
            'number_missing' => [
                'attribute' => 'number',
                'message' => Yii::t('app', 'If you choose a point of sale without electronic billing you must especify a bill number')
            ],
            'number_duplicated' => [
                'attribute' => 'number',
                'message' => Yii::t('app', 'The bill number already exists') . ': ' .$model->number
            ]
        ];

        $errorMessageKeysArray = explode(',', $errorMessageKeys);
        foreach ($errorMessageKeysArray as $errorMessageKey) {
            $attribute = $errorMessages[$errorMessageKey]['attribute'];
            $message = $errorMessages[$errorMessageKey]['message'];
            $model->addError($attribute, $message);
        }

        return $model;
    }

    public static function getConcatedKeyErrors($model)
    {
        $errors = $model->getErrors();
        $errors_array = array_keys($errors);
        $keys = implode(',', $errors_array);

        return $keys;
    }

    public function getNumberFromPointOfSale(){
        $number = $this->getPointOfSale()->number;
        $zero_qty = '';

        for ($i = 0; $i < (4 -$number); $i++){
            $zero_qty .= '0';
        }
        return $zero_qty.$number;
    }

    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

    /**
     * @return bool
     * Envia el comprobante por email al cliente correspondiente.
     * Se envía un mail por cada correo verificado que tenga
     */
    public function sendEmail($pdfFileName, $email)
    {
        $pointOfSale = $this->getPointOfSale()->number;

        $sender = MailSender::getInstance("COMPROBANTE", Company::class, $this->customer->parent_company_id);
        $send_email1 = true;
        $send_email2 = true;
        $send = true;
        $message_subject = "Envio de factura de: " . $this->customer->parentCompany->name;
        $message = [
            'params'=>[
                'image'         => Yii::getAlias("@app/web/". $this->customer->parentCompany->getLogoWebPath()),
                'comprobante'   => $this->billType->name . " " . sprintf("%08d", $this->number )
            ]
        ];

        if (empty($email)) {
            if($this->customer->email_status == Customer::EMAIL_STATUS_ACTIVE) {
                $send_email1 = $sender->send( $this->customer->email, $message_subject, $message,[], [],[$pdfFileName]) ? true : false ;
            }
            
            if($this->customer->email2_status == Customer::EMAIL_STATUS_ACTIVE) {
                $send_email2 = $sender->send( $this->customer->email2, $message_subject, $message,[], [],[$pdfFileName]) ? true : false ;;
            }
        } else {
            $send = $sender->send($email, $message_subject, $message,[], [],[$pdfFileName]) ? true : false ;
            return $send;
        }

        return $send_email1 && $send_email2;
    }

    /**
     * @return bool
     * Actualiza el CAE y fecha de vencimiento de un comprobante sólo si esta en estado closed y no tiene los datos.
     */
    public function updateEinAndEinExpiration($ein, $ein_expiration)
    {
        if($this->status != 'closed') {
            return false;
        }

        if($this->ein && $this->ein_expiration) {
            return false;
        }

        $this->ein = $ein;
        $this->ein_expiration = $ein_expiration;
        return $this->save(['ein' => $ein, 'ein_expiration' => $ein_expiration]);
    }

    /**
     * @param bool $update_amounts
     * @return bool
     * Verifica que los importes del comprobantes estén correctos, tiene opción para volver a calcularlos en caso de que estén erróneos.
     * //TODO hacer verificaciones de los detalles e importes de los mismos. Verificaciones para evitar errores en afip.
     */
    public function verifyAmounts($update_amounts = false) {

        if($this->amount == 0 || $this->taxes == 0 || $this->total == 0 || ($this->amount + $this->taxes != $this->total)) {
            if($update_amounts) {
                $this->updateAmounts();
            }
            return false;
        }

        return true;
    }

    public function verifyNumberAndDate()
    {
        $lastNumber = Bill::find()->where([
            'bill_type_id' => $this->bill_type_id,
            'status' => 'closed',
            'company_id' => $this->company_id
        ])->orderBy(['number' => SORT_DESC])
            ->limit(1)->one();

        $today = (new \DateTime('now'))->format('Y-m-d');

        if($this->date != $today || $this->number != $lastNumber->number){
            $this->updateAttributes(['number' => (int) $lastNumber->number + 1, 'date' => $today]);
        }

    }


    /**
     * Indica si la factura se abonara por debito directo
     */
    public function hasDirectDebit() {
        if ($this->customer) {
            $hasDebit = AutomaticDebit::find()->andWhere(['customer_id' => $this->customer_id, 'status' => AutomaticDebit::ENABLED_STATUS])->one();

            if ($hasDebit && $hasDebit->created_at < $this->timestamp) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return bill for id
     */
    public static function findBillForId($bill_id){
        return self::find()->where(['bill_id' => $bill_id])->one();
    }
    

    /**
     * Finds the Bill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bill::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    // selects the PDF generation library based on the APP configuration items.
    public function makePdf($id){
        // gets conf item
        $pdf_company = Config::getConfig('pdf_company')->description;
        
        if($pdf_company == "westnet")
            return $this->WestnetPdf($id);
        
        else if($pdf_company == "bigway")
            return $this->BigwayPdf($id);
        
    }

    /**
     * Prints the pdf of a single Bill Westnet.
     * @param integer $id
     * @return mixed
     */
    public function WestnetPdf($id)
    {

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-type: application/pdf');
        $response->setDownloadHeaders('bill.pdf', 'application/pdf', true);

        $model = $this->findModel($id);
        $companyData = $model->company;

        $this->layout = '//pdf';

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        $jsonCode = [
           "ver" => 1,
           "fecha" => $model->date,
           "cuit" => str_replace("-","",$companyData->tax_identification),
           "ptoVta" => $model->getPointOfSale()->number,
           "tipoCmp" => $model->billType->code,
           "nroCmp" => $model->number,
           "importe" => $model->total,
           "moneda" => "PES",
           "ctz" => 1,
           "tipoDocRec" => $model->customer->documentType->code,
           "nroDocRec" => str_replace("-","",$model->customer->document_number),
           "tipoCodAut" => "E",
           "codAut" => $model->ein
        ];
        $qrCode = (new QrCode("https://www.afip.gob.ar/fe/qr/?p=".base64_encode(json_encode($jsonCode))))
        ->setSize(500)
        ->setMargin(5);

        $view = $this->render('pdf', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'qrCode' => $qrCode

        ]);

        $pdf = ' ';

        try{
            $pdf = \app\components\helpers\PDFService::makePdf($view);
        } catch (\Exception $ex){
            \Yii::trace($ex);
        }

        return $pdf;
    }

    /**
     * Prints the pdf of a single Bill Bigway.
     * @param integer $id
     * @return mixed
     */
    public function BigwayPdf($id)
    {      
        $model = $this->findModel($id);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $model->getBillDetails(),
            'pagination' => false
        ]);

        $formatter = Yii::$app->formatter;
        $cupon_bill_types = explode(',', \app\modules\config\models\Config::getValue('cupon_bill_types'));
        $is_cupon = (array_search($model->bill_type_id, $cupon_bill_types) !==false);
        $payment = new Payment();
        $payment->customer_id = $model->customer_id;
        $debt = $payment->accountTotal();
        $isConsumidorFinal = false;
        $profile = $model->customer->getCustomerProfiles()->where(['name'=>'Consumidor Final'])->one();
        $company = (isset($company) ? $company : $model->customer->parentCompany );
        $companyData = $model->company;

        //echo'<pre>'; var_dump( $companyData->name  ); die;

        $cuit = str_replace('-', '', $model->company->tax_identification);
        $code = $cuit . sprintf("%02d", $model->billType->code) . sprintf("%04d", $model->getPointOfSale()->number) . $model->ein . (new \DateTime($model->ein_expiration))->format("Ymd");

        $barcode = new BarcodeGeneratorPNG();

        $jsonCode = [
                       "ver" => 1,
                       "fecha" => $model->date,
                       "cuit" => str_replace("-","",$companyData->tax_identification),
                       "ptoVta" => $model->getPointOfSale()->number,
                       "tipoCmp" => $model->billType->code,
                       "nroCmp" => $model->number,
                       "importe" => $model->total,
                       "moneda" => "PES",
                       "ctz" => 1,
                       "tipoDocRec" => $model->customer->documentType->code,
                       "nroDocRec" => str_replace("-","",$model->customer->document_number),
                       "tipoCodAut" => "E",
                       "codAut" => $model->ein
                    ];
        $qrCode = (new QrCode("https://www.afip.gob.ar/fe/qr/?p=".base64_encode(json_encode($jsonCode))))
        ->setSize(500)
        ->setMargin(5);

        $content = $this->renderPartial('bigway-pdf.php',[
            'model' => $model,
            'dataProvider' => $dataProvider,
            'formatter' => $formatter,
            'cupon_bill_types' => $cupon_bill_types,
            'is_cupon' => $is_cupon,
            'payment' => $payment,
            'debt' => $debt,
            'isConsumidorFinal' => $isConsumidorFinal,
            'profile' => $profile,
            'company' => $company,
            'companyData' => $companyData,
            'barcode' => $barcode,
            'code' => $code,
            'qrCode' => $qrCode

        ]);

            
        $pdf = new Pdf([
               
            'mode' => Pdf::MODE_UTF8, 
            
            'format' => Pdf::FORMAT_LEGAL, 
           
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            
            'destination' => Pdf::DEST_BROWSER, 
           
            'content' => $content,  
            'filename' => "documento.pdf",
            'cssFile' => '@app/modules/sale/web/css/sale-bill-pdf.css',
            
            'options' => ['title' => ""],
            
            'methods' => [ 
                'SetTitle' => '',
                'SetFooter'=>['Página {PAGENO} de {nb}'],
            ],
            'marginTop' => 5,
        ]);

            
        return $pdf->render();   
    }

    /**
     * Get all bills where "Tuvo Error" is true and status is != from "closed"
     */
    public function getErrorAndUnclosedBillsQuery() {
        $billsWithErrors = self::find()
                ->alias('b')
                ->where(['!=', 'b.status','closed'])
                ->andWhere(['b.had_error' => true])
                ;
        return $billsWithErrors;
    }

}
