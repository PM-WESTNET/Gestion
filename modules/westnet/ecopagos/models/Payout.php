<?php

namespace app\modules\westnet\ecopagos\models;

use app\components\db\ActiveRecord;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\checkout\models\PaymentMethod;
use app\modules\config\models\Config;
use app\modules\sale\models\Customer;
use app\modules\westnet\ecopagos\components\EscPrinter;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\controllers\CustomerController;
use app\modules\westnet\ecopagos\frontend\FrontendModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * This is the model class for table "payout".
 *
 * @property integer $payout_id
 * @property integer $payment_id
 * @property integer $customer_id
 * @property integer $ecopago_id
 * @property integer $cashier_id
 * @property integer $batch_closure_id
 * @property integer $daily_closure_id
 * @property string $customer_number
 * @property double $amount
 * @property string $date
 * @property string $time
 * @property integer $datetime
 * @property string $number
 * @property string $status
 * @property integer $copy_number
 *
 * @property Cashier $cashier
 * @property Ecopago $ecopago
 * @property Customer $customer
 * @property Payment $payment
 * @property DailyClosure $dailyClosure
 * @property BatchClosure $batchClosure
 * @property BatchClosure[] $batchClosures
 * @property BatchClosureHasPayout[] $batchClosureRelations
 */
class Payout extends ActiveRecord {

    //Statuses
    const STATUS_VALID = 'valid';
    const STATUS_REVERSED = 'reversed';
    const STATUS_CLOSED = 'closed';
    const STATUS_CLOSED_BY_BATCH = 'closed_by_batch';
    //Payment methods
    const PAYMENT_METHOD_CASH = 'Contado';
    //Payout concepts
    const CONCEPT_ECOPAGO = 'Ecopago Payout';
    const CONCEPT_INTERNET = 'Internet subscription';
    //Scenarios
    const SCENARIO_SEARCH = 'search';
    const SCENARIO_BATCHCLOSE = 'batch';

    public $user;

    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'payout';
    }

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbecopago');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ecopago_id', 'cashier_id', 'amount', 'customer_number'], 'required'],
            [['ecopago_id', 'cashier_id', 'customer_number'], 'required', 'on' => Payout::SCENARIO_BATCHCLOSE],
            [['payment_id', 'customer_id', 'ecopago_id', 'cashier_id', 'datetime'], 'integer'],
            [['amount'], 'number'],
            [['amount'], 'number', 'max' => Config::getValue('ecopago_payout_limit'), 'on' => 'default'],
            [['date', 'time', 'cashier', 'ecopago', 'customer_number', 'status', 'copy_number'], 'safe'],
            [['date'], 'date'],
            [['date'], 'date', 'on' => Payout::SCENARIO_SEARCH],
            [['number'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'payout_id' => EcopagosModule::t('app', 'Payment'),
            'payment_id' => EcopagosModule::t('app', 'Payment'),
            'customer_id' => EcopagosModule::t('app', 'Customer'),
            'ecopago_id' => EcopagosModule::t('app', 'Ecopago'),
            'cashier_id' => EcopagosModule::t('app', 'Cashier'),
            'amount' => EcopagosModule::t('app', 'Amount'),
            'date' => EcopagosModule::t('app', 'Date'),
            'time' => EcopagosModule::t('app', 'Time'),
            'datetime' => EcopagosModule::t('app', 'Datetime'),
            'number' => EcopagosModule::t('app', 'Number'),
            'customer_number' => EcopagosModule::t('app', 'Customer number'),
            'cashier' => EcopagosModule::t('app', 'Cashier'),
            'ecopago' => EcopagosModule::t('app', 'Ecopago'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCashier() {
        return $this->hasOne(Cashier::className(), ['cashier_id' => 'cashier_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayment() {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEcopago() {
        return $this->hasOne(Ecopago::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDailyClosure() {
        return $this->hasOne(DailyClosure::className(), ['daily_closure_id' => 'daily_closure_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBatchClosure() {
        return $this->hasOne(BatchClosure::className(), ['batch_closure_id' => 'batch_closure_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBatchClosures() {
        return $this->hasMany(BatchClosure::className(), ['batch_closure_id' => 'batch_closure_id'])->viaTable('batch_closure_has_payout', ['payout_id' => 'payout_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBatchClosureRelations() {
        return $this->hasMany(BatchClosureHasPayout::className(), ['payout_id' => 'payout_id']);
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public function fetchStatuses() {
        return [
            Payout::STATUS_VALID => EcopagosModule::t('app', 'Valid'),
            Payout::STATUS_REVERSED => EcopagosModule::t('app', 'Reversed'),
            Payout::STATUS_CLOSED => EcopagosModule::t('app', 'Closed'),
            Payout::STATUS_CLOSED_BY_BATCH => EcopagosModule::t('app', 'Closed by batch'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public static function staticFetchStatuses() {
        return [
            Payout::STATUS_VALID => EcopagosModule::t('app', 'Valid'),
            Payout::STATUS_REVERSED => EcopagosModule::t('app', 'Reversed'),
            Payout::STATUS_CLOSED => EcopagosModule::t('app', 'Closed'),
            Payout::STATUS_CLOSED_BY_BATCH => EcopagosModule::t('app', 'Closed by batch'),
        ];
    }

    /**
     * Returns current logged in cashier
     * @return Cashier
     * @throws Exception
     */
    public function getCurrentCashier() {
        $cashier = UserHelper::getCashier();

        if (!empty($cashier) && $cashier->isActive()) {
            return $cashier;
        } else {
            throw new HttpException('403', FrontendModule::t('app', 'Cannot find active cashier. Please, log in.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
           
            
            $this->formatDatesBeforeSave();

            if ($insert) {    //If this is a new instance
                if(self::validatePayout($this->customer_number)){
                    $this->datetime = time();
                    $this->date = date('Y-m-d');
                    $this->time = date('H:i:s');

                    //Creates a payment instance for this payout
                    $this->createPayment();
                }else{
                    return false;
                }
            } else {          //If it is not a new instance
                
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if ($this->daily_closure_id == NULL) {

            if ($this->status != 'reversed' && $insert) {

                $dailyClosure = $this->cashier->currentDailyClosure();
                if ($dailyClosure->first_payout_number == 0) {
                    $dailyClosure->first_payout_number = $this->payout_id;
                }
                $dailyClosure->last_payout_number = $this->payout_id;
                $dailyClosure->payment_count += 1;
                $dailyClosure->total += $this->amount;
                $dailyClosure->save();

                $saveTime = Yii::$app->session->get('saveTimes') + 1;
                Yii::$app->session->set('saveTimes', $saveTime);
            } 
        }
    }

    /**
     * Creates the payment associated with this payout
     */
    private function createPayment() {

        $customer = Customer::find()
                        ->orWhere(['customer.code' => $this->customer_number])
                        ->orWhere(['customer.payment_code' => $this->customer_number])->one();

        if (!empty($customer)) {

            $this->customer_id = $customer->customer_id;

            //Base payment creation
            $payment = new Payment();
            $payment->company_id = $customer->company_id;
            $payment->customer_id = $this->customer_id;
            $payment->concept = EcopagosModule::t('app', static::CONCEPT_ECOPAGO);
            $payment->date = $this->date;
            $payment->amount = $this->amount;
            $payment->partner_distribution_model_id = $customer->company->partner_distribution_model_id;

            //Try to save this payment
            if ($payment->save()) {

                //Set payment_id to this ecopago payout
                $this->payment_id = $payment->payment_id;

                //Creating item for payment details
                $paymentDetail = new PaymentItem();

                //Sets payment method as "cash"
                $paymentMethod = PaymentMethod::find()->where([
                            'name' => Config::getConfig('payment_method')->value,
                            'status' => 'enabled',
                        ])->one();

                $moneyBoxAccount = MoneyBoxAccount::findOne(['account_id' => $this->ecopago->account_id]);
                if (!empty($paymentMethod)) {
                    $paymentDetail->payment_id = $payment->payment_id;
                    $paymentDetail->amount = $this->amount;
                    $paymentDetail->description = EcopagosModule::t('app', static::CONCEPT_ECOPAGO);
                    $paymentDetail->payment_method_id = $paymentMethod->payment_method_id;
                    if ($moneyBoxAccount) {
                        $paymentDetail->money_box_account_id = $moneyBoxAccount->money_box_account_id;
                    }
                    $paymentDetail->save(false);
                }

                $payment->close();
            }
        }
    }

    /**
     * Checjs whether this payout is closed or not
     * @return boolean
     */
    public function isClosed() {
        if ($this->status == static::STATUS_CLOSED || $this->status == static::STATUS_CLOSED_BY_BATCH)
            return true;
        else
            return false;
    }

    /**
     * Checks whether this payout is active or not
     * @return boolean
     */
    public function isValid() {
        if ($this->status == static::STATUS_VALID)
            return true;
        else
            return false;
    }

    /**
     * Reopens a payout from a batch closure cancelation
     * @return boolean
     */
    public function reopen() {

        //If this payment is reopenable, we make a try for reopen it
        if ($this->isReopenable()) {

            $this->batch_closure_id = null;

            //If this payout has not a daily closure id, we can safely make this payout as valid again
            if (empty($this->daily_closure_id))
                $this->status = static::STATUS_VALID;

            //If not, we need to set this status to closed, there is a daily closure that contains this payout
            else
                $this->status = static::STATUS_CLOSED;

            if ($this->save())
                return true;
            else
                return false;
        }

        return false;
    }

    /**
     * Reverses a payout (cancels it)
     * @return boolean
     */
    public function reverse() {
        Yii::$app->session->set('saveTimes', 1);
        //If this payment is reversable, we make a try for reversing it
        if ($this->isReversable()) {

            $this->status = Payout::STATUS_REVERSED;

            //Change status of the associated payment of this payout to "draft" instead of "closed" and try to save it
            //$this->payment->status = \app\modules\checkout\models\Payment::STATUS_DRAFT;
            $this->payment->status = 'cancelled';
            if (!$this->payment->save()){
                return false;
            }

            if ($this->save()) {
                $dailyClosure = $this->cashier->currentDailyClosure();
                $dailyClosure->total -= $this->amount;
                $dailyClosure->save();
                return true;
            } else
                return false;
        }

        return false;
    }

    /**
     * Closes a payment
     * @param type $byBatchClosure
     * @return boolean
     */
    public function close($byBatchClosure = true) {

        if ($this->isClosable()) {

            //If it is a batch closure what we are executing, we close this payout by a batch closure
            if ($byBatchClosure) {
                $this->status = Payout::STATUS_CLOSED_BY_BATCH;
            }

            //If this payout is not closed by a batch closure, we simply close it
            if ($this->status != static::STATUS_CLOSED_BY_BATCH)
                $this->status = Payout::STATUS_CLOSED;

            if ($this->save())
                return true;
            else
                return false;
        }

        return false;
    }

    /**
     * Verifies if this payment is reopenable
     * @return boolean
     */
    public function isReopenable() {

        if ($this->status != Payout::STATUS_VALID)
            return true;

        return false;
    }

    /**
     * Verifies if this payment is reversable (cancelable)
     * @return boolean
     */
    public function isReversable() {

        if ($this->status == Payout::STATUS_CLOSED_BY_BATCH)
            return false;

        if ($this->status == Payout::STATUS_CLOSED)
            return false;

        if ($this->status != Payout::STATUS_REVERSED)
            return true;

        return false;
    }

    /**
     * Verifies if this payment is closable
     * @return boolean
     */
    public function isClosable() {
        return true;
    }

    /**
     * Builds a print layout for printer
     * Limite de 40 caracteres
     * @return string
     */
    public function getPrintLayout() {
        $buffer = [];
        
        $payment = new Payment();
        $payment->customer_id = $this->customer->customer_id;

        $due = $payment->accountTotal();
        $due = round(abs($due < 0 ? $due : 0 ), 2);
        

        $company = ($this->customer->parentCompany ? $this->customer->parentCompany : $this->customer->company );
        $printer = new EscPrinter();
        $printer->writeText("      ********** ".$company->name." **********");
        $printer->feed();
        $printer->writeText("COMPROBANTE DE PAGO");
        $printer->feed();
        $printer->writeText("Pago " . $this->payout_id . " | " . $this->payment_id);
        $printer->feed(2);

        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();

        $printer->writeText("Cliente:" . $this->customer_number);
        $printer->feed();

        $printer->writeText((!$this->customer_id) ? '' : $this->customer->fullName);
        $printer->feed();

        $printer->writeText("Monto pagado: $" . $this->amount . ".-");
        $printer->feed(2);
        
              
        if ($due > 0) {
           // $printer->writeText("Saldo Pendiente: $" . $due . ".-");
            //$printer->feed(2);
        }
        
        

        $printer->writeText("PARA CUALQUIER CONSULTA LLAME AL");
        $printer->feed();
        $printer->writeText($company->phone . " o INGRESE A LA PAGINA:");
        $printer->feed();

        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();

        $printer->writeText($company->portal_web);
        $printer->feed();
        $printer->writeText("Cobrador: " . $this->cashier->getCompleteName());

        $printer->writeText("Sucursal: " . $this->ecopago->name);
        $printer->feed();

        $printer->writeText("Fecha: " . date("H:i", $this->datetime) . 'hs ' . date("d/m/Y", $this->datetime));
        $printer->feed();

        $printer->writeText("Copia Numero: " . $this->copy_number);
        $printer->feed(10);

        $buffer[] = $printer->getBuffer();

        return json_encode($buffer);
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Cashier, Ecopago.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    public function canView($oldId) {
        if ($this->cashier_id == $oldId) {
            return true;
        } else {
            return false;
        }
    }

    public function incrementNumberCopy() {
        $this->copy_number = $this->copy_number + 1;
        $this->updateAttributes(['copy_number' => $this->copy_number++]);
    }

    public static function countReversed() {
        $current_daily_closure = DailyClosure::find()->where(['status' => 'open'])->one();
        if ($current_daily_closure) {
            $reversed = self::find()->where(['status' => 'reversed'])
                    ->andWhere(['>=', 'payout_id', $current_daily_closure->first_payout_number])
                    ->andWhere(['<=', 'payout_id', $current_daily_closure->last_payout_number])
                    ->count();

            return $reversed;
        } else {
            return 0;
        }
    }
    
    public static function validatePayout($customer_number){
        $customer= (new ActiveQuery(Customer::className()))
                        ->select(['customer_id'])
                        ->from(['customer'])
                        ->where(['code' => $customer_number])
                        ->orWhere(['payment_code' => $customer_number])
                        ->one();

        if (empty($customer)) {
            return false;
        }

        $lastPayment= (new ActiveQuery(self::className()))
                    ->andwhere(['customer_id' => $customer->customer_id]) 
                    ->andWhere(['like', 'status', 'valid']) 
                    ->andWhere(['date' => date('Y-m-d')])
                    ->andWhere(['ecopago_id' => UserHelper::getEcopago()->ecopago_id,])
                    ->andWhere(['cashier_id' => UserHelper::getCashier()->cashier_id])
                    ->one();
        
        
        return empty($lastPayment);
    }

}
