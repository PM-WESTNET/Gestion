<?php

namespace app\modules\westnet\ecopagos\models;

use app\modules\westnet\ecopagos\components\EscPrinter;
use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * This is the model class for table "daily_closure".
 *
 * @property integer $daily_closure_id
 * @property integer $datetime
 * @property integer $cashier_id
 * @property integer $ecopago_id
 * @property string $first_payout_number
 * @property string $last_payout_number
 * @property integer $payment_count
 * @property double $total
 * @property string $date
 * @property string $time
 * @property string $status
 * @property integer $close_datetime
 *
 * @property Cashier $cashier
 * @property Ecopago $ecopago
 * @property Payout[] $payouts
 */
class DailyClosure extends \app\components\db\ActiveRecord {

    //Scenarios
    const SCENARIO_FRONTEND = 'frontend';
    //Statuses
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELED = 'canceled';

    //Payouts for information
    public $firstPayout;
    public $lastPayout;
    public $tempPayouts;
    //Flags 
    public $hasPayments = false;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'daily_closure';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbecopago');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cashier_id', 'ecopago_id'], 'required'],
            [['datetime', 'cashier_id', 'payment_count', 'close_datetime'], 'integer'],
            [['total'], 'number'],
            [['date', 'time', 'cashier'], 'safe'],
            [['date'], 'date'],
            [['status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ecopago_id' => EcopagosModule::t('app', 'Ecopago'),
            'daily_closure_id' => EcopagosModule::t('app', 'Daily closure'),
            'datetime' => EcopagosModule::t('app', 'Datetime'),
            'cashier_id' => EcopagosModule::t('app', 'Cashier ID'),
            'first_payout' => EcopagosModule::t('app', 'First payout'),
            'last_payout' => EcopagosModule::t('app', 'Last payout'),
            'payment_count' => EcopagosModule::t('app', 'Payment count'),
            'total' => EcopagosModule::t('app', 'Total'),
            'date' => EcopagosModule::t('app', 'Date'),
            'time' => EcopagosModule::t('app', 'Time'),
            'status' => EcopagosModule::t('app', 'Status'),
            'cashier' => EcopagosModule::t('app', 'Cashier'),
            'payouts' => EcopagosModule::t('app', 'Payouts'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public static function staticFetchStatuses() {
        return [
            static::STATUS_OPEN => EcopagosModule::t('app', 'Open'),
            static::STATUS_CLOSED => EcopagosModule::t('app', 'Closed'),
            static::STATUS_CANCELED => EcopagosModule::t('app', 'Canceled'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public function fetchStatuses() {
        return [
            static::STATUS_OPEN => EcopagosModule::t('app', 'Open'),
            static::STATUS_CLOSED => EcopagosModule::t('app', 'Closed'),
            static::STATUS_CANCELED => EcopagosModule::t('app', 'Canceled'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashier() {
        return $this->hasOne(Cashier::className(), ['cashier_id' => 'cashier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopago() {
        return $this->hasOne(Ecopago::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayouts() {
        return $this->hasMany(Payout::className(), ['daily_closure_id' => 'daily_closure_id']);
    }

    /**
     * Opens a cash register for a day
     * @return boolean
     */
    public function openCashRegister() {

        if ($this->isOpenable()) {

            $this->cashier_id = \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier()->cashier_id;
            $this->ecopago_id = \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getEcopago()->ecopago_id;

            $this->status = static::STATUS_OPEN;

            //Empty, we are only opening this daily closure as a cash register daily opening
            $this->first_payout_number = 0;
            $this->last_payout_number = 0;
            $this->payment_count = 0;
            $this->total = 0;

            return $this->save();
        } else {
            return false;
        }
    }

    /**
     * Checks if a cash register can be open
     * @return boolean
     */
    private function isOpenable() {

        if (UserHelper::hasOpenCashRegister())
            return false;

        return true;
    }

    /**
     * Sets preview data for a daily closure
     */
    public function preview() {

        $this->close_datetime = time();

        $payoutSearch = new search\PayoutSearch();
        $payoutQuery = $payoutSearch->queryFindByDailyClosure($this);
        $payouts = $payoutQuery->all();

        if (!empty($payouts)) {         //If there is payments to be closed
            $this->hasPayments = true;
            $this->payment_count = count($payouts);
            $this->total = $payoutQuery->sum('amount');

            $this->first_payout_number = (string) end($payouts)->payout_id;
            $this->firstPayout = end($payouts);
            $this->last_payout_number = (string) $payouts[0]->payout_id;
            $this->lastPayout = $payouts[0];

            $this->tempPayouts = $payouts;
        } else {                        //If there is not payments to be closed
            $this->hasPayments = false;
        }
    }

    /**
     * Closes a daily closure
     * @return boolean
     */
    public function close() {

        if ($this->isClosable()) {

            $this->preview();

            //Process each payment
            if (!empty($this->tempPayouts)) {
                foreach ($this->tempPayouts as $payout) {

                    //Sets the daily closure to this payout and then closes it
                    $payout->daily_closure_id = $this->daily_closure_id;
                    if (!$payout->close(false)){
                        return false;
                    }
                }
            }

            //Set status to "closed"
            $this->status = static::STATUS_CLOSED;
            if (!$this->save()){
                return false;
            }
            
            \Yii::$app->session->set('payout_reversed', 0);
            return true;
        }

        return false;
    }

    /**
     * Checks if the current cash register can be closed
     * @return boolean
     */
    private function isClosable() {
        if (!UserHelper::hasOpenCashRegister())
            return false;

        return true;
    }

    /**
     * Checks if this current cash register is an old one
     * @return boolean
     */
    public function isOld() {
        if ($this->isClosable() && date('Y-m-d', $this->datetime) < date('Y-m-d'))
            return true;
        else
            return false;
    }

    /**
     * Checks if this current daily closure can be canceled
     */
    public function isCancelable() {
        
        //TODO
        return false;

        //Cannot close current open cash register
        if ($this->status == static::STATUS_OPEN)
            return false;

        return true;
    }

    /**
     * Makes an attempt to cancel this daily closure
     */
    public function cancel() {

        //If this batch closure is cancelable
        if ($this->isCancelable()) {

            //Change the status
            $this->status = static::STATUS_CANCELED;

            //Reopen payouts in order to other batch closure can close them
            if ($this->save()) {
                $this->reopenPayouts();
                return true;
            }
        }

        return false;
    }

    /**
     * Reopens each of this batch closure payouts before deleting the closure
     */
    private function reopenPayouts() {

        if (!empty($this->payouts)) {
            foreach ($this->payouts as $payout) {
                $payout->reopen();
                $this->unlink("payouts", $payout);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            //If it is a new instance
            if ($this->isNewRecord) {
                $this->datetime = time();
                $this->date = date('Y-m-d');
                $this->time = date('H:i:s');
            }

            $this->formatDatesBeforeSave();

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
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
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Cashier, Payouts.
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
    
    public function getPrintLayout() {
        $buffer = [];

        $printer = new EscPrinter();

        $printer->writeText( "      ********** WESTNET **********");
        $printer->feed();
        $printer->writeText( "COMPROBANTE DE CIERRE DIARIO");
        $printer->feed();
        $printer->writeText( "Cierre Diario " . $this->daily_closure_id );
        $printer->feed(2);
        $printer->writeText("Cobrador: " . $this->cashier->getCompleteName() );
        $printer->feed(2);

        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();

        $printer->writeText("Cantidad de tickets: " . $this->payment_count );
        $printer->feed(2);
        $printer->writeText("Desde ticket Nº: " . $this->first_payout_number );
        $printer->feed(2);
        $printer->writeText("Hasta ticket Nº: " . $this->last_payout_number );
        $printer->feed(2);
        $printer->writeText("Monto total: $" . $this->total );
        $printer->feed(2);
        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();

        $printer->writeText("PARA CUALQUIER CONSULTA LLAME AL");
        $printer->feed();
        $printer->writeText("0261-4200997 o INGRESE A LA PAGINA:");
        $printer->feed();
        $printer->writeText("clientes.westnet.com.ar");
        $printer->feed();
        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();

        $printer->writeText("Sucursal: " . $this->ecopago->name );
        $printer->feed();
        $printer->writeText("Fecha: " . date("H:i", $this->datetime) . 'hs ' . date("d/m/Y", $this->datetime) );
        $printer->feed(10);

        $buffer[] = $printer->getBuffer();

        return json_encode($buffer);
    }
    
    public function canView($id){
        if ($this->cashier_id == $id) {
            return true;
        }else{
            return false;
        }
    }

}
