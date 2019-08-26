<?php

namespace app\modules\provider\models;

use app\components\db\ActiveRecord;
use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\CountableInterface;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\paycheck\models\Paycheck;
use Codeception\Util\Debug;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "provider_payment".
 *
 * @property integer $provider_payment_id
 * @property string $date
 * @property double $amount
 * @property string $description
 * @property integer $provider_id
 * @property integer $timestamp
 * @property double $balance
 * @property string $status;
 * @property integer $partner_distribution_model_id
 * @property integer $company_id
 *
 * @property ProviderBillHasProviderPayment[] $providerBillHasProviderPayments
 * @property ProviderBill[] $providerBills
 * @property Provider $provider
 * @property PartnerDistributionModel $partnerDistributionModel
 * @property ProviderPaymentItem[] $providerPaymentItems
 */
class ProviderPayment extends \app\components\companies\ActiveRecord implements CountableInterface
{

    private $_providerBills;
    private $_oldBills;

    const STATUS_CREATED = 'created';
    const STATUS_CLOSED = 'closed';
    const STATUS_CONCILED = 'conciled';

    public function __construct($config = array()) {
        parent::__construct($config);

        $this->date = (new \DateTime('now'))->format('d-m-Y');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_payment';
    }

    public function behaviors()
    {
        return [
            'account' => [
                'class'=> 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $statuses = ['created', 'closed', 'tabulated', 'conciled'];

        return [
            [['date', 'providerBills', 'provider', 'company_id'], 'safe'],
            [['date'], 'date'],
            [['balance'], 'number'],
            [['status'], 'string'],
            [['provider_id'], 'required'],
            [['provider_id', 'timestamp','partner_distribution_model_id', 'company_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => $statuses],
            [['status'], 'default', 'value' => 'created'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_payment_id' => Yii::t('app', 'Provider Payment'),
            'date' => Yii::t('app', 'Date'),
            'amount' => Yii::t('app', 'Amount'),
            'description' => Yii::t('app', 'Observations') . ' ' . Yii::t('app', '(optional)'),
            'provider_id' => Yii::t('app', 'Provider'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'balance' => Yii::t('app', 'Balance'),
            'providerBillHasProviderPayments' => Yii::t('app', 'ProviderBillHasProviderPayments'),
            'providerBills' => Yii::t('app', 'Provider Bills'),
            'provider' => Yii::t('app', 'Provider'),
            'status' => Yii::t('app', 'Status'),
            'provider_bill_id' => Yii::t('app', 'Provider Bill'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderBillHasProviderPayments()
    {
        return $this->hasMany(ProviderBillHasProviderPayment::class, ['provider_payment_id' => 'provider_payment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderBills()
    {
        return $this->hasMany(ProviderBill::class, ['provider_bill_id' => 'provider_bill_id'])->viaTable('provider_bill_has_provider_payment', ['provider_payment_id' => 'provider_payment_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['provider_id' => 'provider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    public function getProviderPaymentItems()
    {
        return $this->hasMany(ProviderPaymentItem::className(), ['provider_payment_id' => 'provider_payment_id']);
    }

    /**
     * @brief Sets ProviderBills relation on helper variable and handles events insert and update
     */
    public function setProviderBills($providerBills){

        if(empty($providerBills)){
            $providerBills = [];
        }

        $this->_providerBills = $providerBills;

        $saveProviderBills = function($event){
            $this->unlinkAll('providerBills', true);

            foreach ($this->_providerBills as $id) {
                $this->link('providerBills', ProviderBill::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveProviderBills);
        $this->on(self::EVENT_AFTER_UPDATE, $saveProviderBills);
    }
    
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();
            $this->balance = $this->amount - $this->calculateTotalPayed();
            return true;
        } else {
            return false;
        }     
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
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
        $this->date = Yii::$app->formatter->asDate((!$this->date ? (new \DateTime('now'))->format('d-m-Y'): $this->date ), 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: ProviderBillHasProviderPayments, ProviderBills, PaymentMethod, Provider.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('providerBills', true);
        $this->unlinkAll('providerPaymentItems', true);
        if($this->status != 'created' ){
            AccountMovementRelationManager::delete($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->_oldBills = $this->getProviderBills()->all();
                /*if ($this->paycheck != null) {
                    $this->paycheck->revertState();
                }*/
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $this->updateBalanceBill(false);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->updateBalanceBill();

        if($this->status === 'closed') {
            $this->onClose();
        }
    }

    /**
     * Actualiza el balance de las facturas
     * @param bool|true $update
     */
    public function updateBalanceBill($update=true)
    {
        $bills = ($update ? $this->getProviderBills()->all() : $this->_oldBills );
        foreach ($bills as $bill) {
            $bill->calculateTotal();
        }
    }

    /**
     * Agrega una factura
     *
     * @param $bill
     * @return ProviderBillHasProviderPayment|null|static
     */
    public function addBill($bill)
    {
        if($bill['provider_bill_id']){
            $pay = ProviderBillHasProviderPayment::findOne([
                'provider_bill_id'=> $bill['provider_bill_id'],
                'provider_payment_id'=> $bill['provider_payment_id']]);
        }
        if(empty($pay)) {
            $pay = new ProviderBillHasProviderPayment();
        }
        $pay->setAttributes($bill);
        $pay->save();

        return $pay;
    }

    /**
     * Agrega una factura
     *
     * @param $bill
     * @return ProviderBillItem|null|static
     */
    public function addItem($item)
    {
        if($item['provider_payment_item_id']){
            $itemDb = ProviderPaymentItem::findOne([
                'provider_payment_item_id'=> $item['provider_payment_item_id']]);
        }

        if(empty($itemDb)) {
            $itemDb = new ProviderPaymentItem();
            $itemDb->setAttributes($item);
            $itemDb->validate();
            $itemDb->save();
        } else {
            $itemDb->save();
        }

        $this->calculateTotal();

        return $itemDb;
    }


    /**
     * Calcula el importe total del pago en base a los items.
     *
     * @return float
     */
    public function calculateTotal()
    {
        $amount = 0;
        foreach( $this->getProviderPaymentItems()->all()  as $item) {
            $amount += $item->amount;
        }
        $this->updateAttributes(['amount'=>round($amount,2)]);
        return $amount;
    }

    /**
     * Calcula el importe total del pago
     *
     * @return float
     */
    public function calculateTotalPayed()
    {
        $amount = 0;
        foreach( $this->getProviderBillHasProviderPayments()->all()  as $bill) {
            $amount += ($bill->amount * $bill->providerBill->billType->multiplier);
        }
        return round($amount,2);
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
        $paymentMethods = [];
        $paymentMethods['total'][] = $this->amount;
        foreach($this->providerPaymentItems as $item) {
            $paymentMethods[$item->payment_method_id][] = $item->amount;
        }
        return $paymentMethods;
    }

    /**
     * Verifica si se puede cerrar o no el pago.
     */
    public function canClose()
    {
        $total = round($this->calculateTotal());
        $totalPayed = round($this->calculateTotalPayed());
        return $this->status == 'created' && ( ($total == $totalPayed || $totalPayed==0) && $total != 0  );
    }

    public function verifyItems($newDate=null)
    {
        $modelDate = new \DateTime(($newDate ? $newDate : $this->date ));
        error_log( $modelDate->format('Y-m-d') . " <= " );
        /** @var ProviderPaymentItem $providerPaymentItem */
        foreach ($this->providerPaymentItems as $providerPaymentItem) {
            if($providerPaymentItem->moneyBoxAccount->small_box)  {
                $date = new \DateTime($providerPaymentItem->moneyBoxAccount->daily_box_last_closing_date);

                if($modelDate <= $date ) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     *  Aplica un provider_payment a una o mas provider_bills, de la mas vieja a la mas nueva
     * y actualiza el valor de balance
     */
    public function associateProviderBills($provider_bill_ids)
    {
        $return = true;
        if (count($provider_bill_ids) > 0) {
            $provider_bills = ProviderBill::find()->where(['provider_bill_id' => $provider_bill_ids])->orderBy(['date' => SORT_ASC])->all();
            $saldo = $this->balance;
            foreach ($provider_bills as $provider_bill) {
                if ($saldo > 0) {
                    $debt = $provider_bill->getDebt();

                    $pbhpp = new ProviderBillHasProviderPayment([
                        'provider_bill_id' => $provider_bill->provider_bill_id,
                        'provider_payment_id' => $this->provider_payment_id
                    ]);

                    if ($saldo >= $debt) {
                        $pbhpp->amount = $debt;
                        $saldo -= $debt;
                    } else if ($saldo < $debt) {
                        $pbhpp->amount = $saldo;
                        $saldo = 0;
                    }

                    if(!$pbhpp->save()){
                        $return = false;
                    }
                }
            }
        }
        //Para disparar la actualizacion del balance en el beforesave
        $this->save();

        return $return;
    }

    /**
     * Elimina la asociación con provider bills
     */
    public function disassociateProviderBills($provider_bill_ids)
    {
        if (count($provider_bill_ids) > 0) {
            ProviderBillHasProviderPayment::deleteAll(['provider_payment_id' => $this->provider_payment_id, 'provider_bill_id' => $provider_bill_ids]);
            //Para disparar la actualización del campo balance en el beforesave
            $this->save();
            return true;
        }
    }

    /**
     * Al cerrar el pago, si alguno de los items tiene un cheque como pago, marco el cheque como entregado
     */
    public function onClose()
    {
        foreach ($this->providerPaymentItems as $item) {

            if ($item->payment_method_id == Config::getValue('payment_method_paycheck')) {
                $paycheck =  Paycheck::findOne($item->paycheck_id);

                if ($paycheck) {
                    $paycheck->changeState(Paycheck::STATE_COMMITED);
                }
            }
        }
    }
}