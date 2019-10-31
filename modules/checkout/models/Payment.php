<?php

namespace app\modules\checkout\models;

use app\components\companies\ActiveRecord;
use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\CountableInterface;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\westnet\ecopagos\models\Payout;
use app\modules\westnet\notifications\models\Customer as Customer2;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord as ActiveRecord2;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use app\modules\mailing\components\sender\MailSender;
use app\modules\sale\models\Company;

/**
 * This is the model class for table "payment".
 *
 * @property integer $payment_id
 * @property double $amount
 * @property string $date
 * @property string $time
 * @property integer $timestamp
 * @property string $number
 * @property string $concept
 * @property integer $customer_id
 * @property double $balance
 * @property string $status
 * @property integer $partner_distribution_model_id
 *
 * @property BillHasPayment[] $billHasPayments
 * @property Customer $customer
 * @property PaymentItem[] $paymentItems
 * @property PartnerDistributionModel $partnerDistributionModel
 */
class Payment extends  ActiveRecord  implements CountableInterface
{

    const PAYMENT_DRAFT = 'draft';
    const PAYMENT_CLOSED = 'closed';
    const PAYMENT_TABULATED = 'tabulated';
    const PAYMENT_CONCILED = 'conciled';
    const PAYMENT_CANCELLED = 'cancelled';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $statuses = ['draft','closed', 'tabulated', 'conciled', 'cancelled'];

        $rules = [
            [['date', 'time', 'customer', 'company_id', 'partnerDistributionModel'], 'safe'],
            [['amount'], 'amount', 'on'=>'default'],
            [['amount', 'balance'], 'number', 'on'=>'manual'],
            [['customer_id'], 'required', 'when'=>function(){ return Yii::$app->params['customer_required'] ? true : false; }],
            [['customer_id', 'partner_distribution_model_id', 'number'], 'integer'],
            [['amount'], 'required'],
            [['status'], 'string'],
            [['status'], 'string'],
            [['concept'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => $statuses],
            [['status'], 'default', 'value' => 'draft'],
            ['date', 'cantBeOld'],
            [['date'], 'required'],
            //[['number'], 'register', 'skipOnEmpty' => false],
        ];

        if (Yii::$app->params['companies']['enabled']) {
            $rules[] = [['company_id'], 'required'];
        }
        return $rules;
    }

    /**
     * Valida que el numero de comprobante este presente en caso de que
     * el medio de pago lo requiera.
     * @param string $attribute
     * @param array $params
     */
    public function register($attribute,$params){
        if($this->paymentMethod && $this->paymentMethod->register_number && empty($this->number)){
            $this->addError($attribute, Yii::t('yii','{attribute} cannot be blank.',['attribute'=>$this->getAttributeLabel($attribute)]));
        }

    }

    public function cantBeOld($attribute, $params, $validator)
    {
        if (strtotime($this->$attribute) < strtotime((new \DateTime('first day of -2 month'))->format('d-m-Y'))) {
            $this->addError($attribute, Yii::t('app', 'Debe elegir una fecha posterior a ' . (new \DateTime('first day of -2 month'))->format('d-m-Y'), ['attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function amount($attribute, $params){
        /*
        if(!empty($this->bill)){
            if($this->amount > $this->bill->debt+0.001){
                $this->addError($attribute, Yii::t('yii','{attribute} must be no greater than {max}.',['attribute'=>$this->getAttributeLabel($attribute),'max'=>$this->bill->debt+0.001]));
            }
        }elseif($this->customer && $this->paymentMethod){
            if($this->amount > $this->accountTotalCredit($this->customer,$this->paymentMethod)+0.001){
                $this->addError($attribute, Yii::t('yii','{attribute} must be no greater than {max}.',['attribute'=>$this->getAttributeLabel($attribute),'max'=>$this->accountTotalCredit($this->customer,$this->paymentMethod)+0.001]));
            }
        }*/

    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord2::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('H:i');},
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord2::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'account' => [
                'class'=> 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ],
            'discount' => [
                'class'=> 'app\modules\westnet\components\ReferencedDiscountBehavior'
            ],
            'ticket' => [
                'class'=> 'app\modules\ticket\behaviors\TicketBehavior'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_id' => Yii::t('app', 'ID'),
            'amount' => Yii::t('app', 'Amount'),
            'type' => Yii::t('app', 'Type'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'concept' => Yii::t('app', 'Concept'),
            'customer' => Yii::t('app', 'Customer'),
            'customer_id' => Yii::t('app', 'Customer'),
            'bill_id' => Yii::t('app', 'Bill ID'),
            'bill' => Yii::t('app', 'Bill'),
            'number' => Yii::t('app', 'Ticket Number'),
            'balance' => Yii::t('app', 'Balance'),
            'paymentMethod' => Yii::t('app','Payment Method'),
            'status' => Yii::t('app','Status'),
            'company_id' => Yii::t('app','Company'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
        ];
    }

    /**
     * @param $bill int
     */
    public function setBill($bill){

        $this->bill_id = $bill->bill_id;
        $this->amount = $bill->debt;
        $this->concept = Yii::t('app','Bill pay:') . " $bill->number";
        $this->customer_id = $bill->customer_id;

        if (Yii::$app->params['companies']['enabled']) {
            $this->company_id = $bill->company_id;
        }

        if(Yii::$app->params['customer_required'] == true && empty($this->customer_id)){
            throw new HttpException(500, 'Customer required.');
        }

    }

    /**
     * @return ActiveQuery
     */
    public function getBillHasPayments()
    {
        return $this->hasMany(BillHasPayment::className(), ['payment_id' => 'payment_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getPaymentItems()
    {
        return $this->hasMany(PaymentItem::className(), ['payment_id' => 'payment_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * Si es un pago en cuenta corriente, registramos "debt" como true
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){

            $this->formatDatesBeforeSave();
            $this->balance = $this->amount - $this->calculateTotalApplied();

            //TODO ver como afecta el debt
            /*if($this->paymentMethod->type == 'account'){
                $this->debt = true;
            }*/

            return true;

        }else{
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        //TODO ver como afecta esto
        /*
        if($this->bill_id){
            $this->bill->checkPayment();
        }*/
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->formatDatesAfterFind();
        $this->getEcoPago();
        parent::afterFind();
    }

    /**
     * @brief Deletes weak relations for this model on delete
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('billHasPayments', true);
        $this->unlinkAll('paymentItems', true);

        AccountMovementRelationManager::delete($this);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

    /**
     * Calcula el total del importe cargado en los items
     *
     * @return int|mixed
     */
    public function calculateTotalItems()
    {
        $total = 0;

        foreach($this->getPaymentItems()->all() as $item) {
            $total += $item->amount;
        }

        return $total;
    }

    /**
     * Calcula el total del importe aplicado a comprobantes
     *
     * @return int|mixed
     */
    public function calculateTotalApplied()
    {
        $total = 0;

        foreach($this->getBillHasPayments()->all() as $item) {
            $total += $item->amount;
        }

        return $total;
    }

    /**
     *
     * @param type $customer
     * @param type $method
     * @return type
     */
    public function accountTotal($fromDate = null, $toDate = null, $only_closed = true){

        return $this->accountPayed($fromDate, $toDate, $only_closed) - $this->accountTotalCredit($fromDate, $toDate, $only_closed);

    }

    /**
     * Retorna el total pagado para el customer.
     * Tiene en cuenta todos los pagos realizados.
     *
     * @param string $fromDate
     * @param string $toDate 
     * @return float|mixed
     */
    public function accountPayed($fromDate = null, $toDate = null, $only_closed = false)
    {
        $qMethodPayment = (new Query())->select(['payment_method_id'])
            ->from('payment_method')
            ->where(['=', 'type', 'account']);

        $query = Payment::find()
            ->leftJoin('payment_item pi', 'payment.payment_id = pi.payment_id')
            ->where(['NOT IN', 'pi.payment_method_id'  , $qMethodPayment])
            ->andWhere(['customer_id'=>$this->customer_id]);

        if($only_closed) {
            $query->andWhere(['status' => Payment::PAYMENT_CLOSED]);
        }
        
        if($fromDate !== null){
            $query->andWhere("date>='$fromDate'");
        }
        
        if($toDate !== null){
            $query->andWhere("date<='$toDate'");
        }

        $payed = $query->sum( new Expression('coalesce(pi.amount, pi.amount, payment.amount)'));

        return abs($payed) > 0.0 ? $payed : 0.0;

    }

    /**
     * Retorna el total de deuda de un customer, teniendo en cuenta la cuenta corriente.
     * La suma se calcula teniendo en cuenta el multiplicador del tipo de comprobantes.
     *
     * @return float|mixed
     */
    public function accountTotalCredit($fromDate = null, $toDate = null, $only_closed = true)
    {

        $query = Bill::find();
        $query->leftJoin("bill_type", 'bill.bill_type_id = bill_type.bill_type_id' );
        $query->where([
            'bill.customer_id' => $this->customer_id,

        ]);


        if ($only_closed) {
            $query->andWhere([
                'bill.status' => Bill::STATUS_CLOSED
            ]);
        }

        if($fromDate !== null){
            $query->andWhere("date>='$fromDate'");
        }
        
        if($toDate !== null){
            $query->andWhere("date<='$toDate'");
        }

        $debt = $query->sum('(bill.total * bill_type.multiplier)');

        return abs($debt) > 0.0 ? $debt : 0.0;
    }

    /**
     * Modificar en caso de que el modelo no pueda ser eliminado
     * @return boolean
     */
    public function getDeletable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        return true;
    }

    /**
     * Modificar en caso de que el modelo no pueda ser actualizado
     * Si el modelo se encuentra en la relacion de una cuenta monetaria no puede ser elimminado.
     * @return boolean
     */
    public function getUpdatable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        if ($this->status != self::PAYMENT_DRAFT) {
            return false;
        }

        return true;
    }

    public function getConfig()
    {
        $query = PaymentMethod::find();
        $query->select(['payment_method_id', 'name']);
        $paymentMethods = ArrayHelper::map($query->asArray()->all(), 'payment_method_id', 'name');
        $paymentMethods['total'] = 'Total';
        return $paymentMethods;
    }

    public function getAmounts()
    {
        $paymentMethods = $this->getConfig();
        array_walk($paymentMethods, function(&$value, $key){$value=0;});
        foreach($this->paymentItems as $item) {
            $paymentMethods[$item->payment_method_id] = $item->amount;
        }
        $paymentMethods['total'] = $this->amount;
        return $paymentMethods;
    }

    public function close()
    {
        if ($this->status == 'draft') {
            $number = Payment::find()->where(['company_id' => $this->company_id])->max('number') + 1;
            $this->number = $number;
            $this->status = 'closed';
            $this->update(false);
            return true;
        }
        return false;
    }

    /**
     * Agrega un Item al pago
     *
     * @param $item
     * @return PaymentItem|null|static
     */
    public function addItem($item_payment)
    {
        if($item_payment['payment_id']){
            $item = PaymentItem::findOne([
                'payment_id'=> $item_payment['payment_id'],
                'amount'=> $item_payment['amount'],
                'description'=> $item_payment['description'],
                'payment_method_id'=> $item_payment['payment_method_id'],
                'paycheck_id'=> $item_payment['paycheck_id'],
                'money_box_account_id'=> $item_payment['money_box_account_id'],
            ]);
        }
        if(empty($item)) {
            \Yii::trace('no encuentra');
            $item = new PaymentItem();
            $item->setAttributes($item_payment);
            \Yii::trace($item);
            $item->save();
            $a = $this->link('paymentItems', $item);

            \Yii::trace($item->getErrors());
        } else {
            \Yii::trace('encuentra');
            \Yii::trace($item);
            $item->save();
        }

        \Yii::trace($item->getErrors());

        return $item;
    }

    /**
     * Aplica el pago a las facturas enviadas como parametro, desde la mas vieja a la mas
     * nueva, en caso de que se aplicò todo el saldo del pago, las demas facturas no son
     * tenidas en cuenta.
     *
     * @param $bill_ids
     * @return bool
     */
    public function applyToBill($bill_ids)
    {
        if (count($bill_ids)>0) {
            $bills = Bill::find()->where(['bill_id'=>$bill_ids])->orderBy(['date'=>SORT_ASC])->all();
            $saldo = $this->balance;
            foreach ($bills as $bill) {
                if ($saldo > 0) {
                    $debt = $bill->getDebt();
                    $bhp = new BillHasPayment();
                    $bhp->bill_id = $bill->bill_id;

                    if ($saldo >= $debt) {
                        $bhp->amount = $debt;
                        $saldo -= $debt;
                    } else if ($saldo < $debt) {
                        $bhp->amount = $saldo;
                        $saldo = 0;
                    }
                    $this->link('billHasPayments', $bhp);
                }
            }
            $this->save();
            return true;
        }
    }

    public function disengageBill($bill_ids)
    {
        if (count($bill_ids)>0) {
            BillHasPayment::deleteAll(['bill_has_payment_id'=>$bill_ids]);
            $this->save();
            return true;
        }
    }

    /**
     * Verifica si se puede cerrar o no el pago.
     */
    public function canClose()
    {

        return (round($this->calculateTotalItems()) == round($this->amount) && round($this->amount) != 0 ) && $this->status == 'draft';
    }
    
    /**
     * 
     * Verifica si el pago fue echo por un ecopago. Devuelve el ecopago si el pago
     * esta asociado a un ecopago o null si no fue echo en un ecopago
     */    
    public  function getEcoPago(){
        $payOut= Payout::findOne(['payment_id' => $this->payment_id]);
        if ($payOut !== null) {
            return $payOut->ecopago;
        }else{
            return null;
        }
        
    }

    public static function getLastNumber($company_id){
        $number = Payment::find()->where(['company_id' => $company_id])->max('number');
        return $number;
    }

    /**
     * @return bool
     * Envia el comprobante por email al cliente correspondiente.
     */
    public function sendEmail($pdfFileName)
    {
        $sender = MailSender::getInstance("COMPROBANTE", Company::class, $this->customer->parent_company_id);

        if ($sender->send( $this->customer->email, "Envio de comprobante", [
            'params'=>[
                'image'         => Yii::getAlias("@app/web/". $this->customer->parentCompany->getLogoWebPath()),
                'comprobante'   => sprintf("%08d", $this->number )
            ]],[], [],[$pdfFileName]) ) {
            return true;
        }
        return false;
    }
}
