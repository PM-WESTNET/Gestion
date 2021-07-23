<?php

namespace app\modules\westnet\ecopagos\models;

use app\components\db\ActiveRecord;
use app\modules\accounting\components\CountableInterface;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\sale\models\Company;
use app\modules\westnet\ecopagos\components\BatchClosureService;
use app\modules\westnet\ecopagos\components\EscPrinter;
use app\modules\westnet\ecopagos\EcopagosModule;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\modules\westnet\ecopagos\models\search\PayoutSearch;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\modules\accounting\components\AccountMovementRelationManager;

/**
 * This is the model class for table "batch_closure".
 *
 * @property integer $batch_closure_id
 * @property integer $last_batch_closure_id
 * @property integer $ecopago_id
 * @property integer $collector_id
 * @property string $date
 * @property string $time
 * @property integer $datetime
 * @property string $number
 * @property double $total
 * @property integer $payment_count
 * @property string $first_payout_number
 * @property string $last_payout_number
 * @property double $commission
 * @property double $discount
 * @property string $status
 * @property double $real_total
 * @property integer $money_box_account_id
 * @property double $difference
 *
 * @property BatchClosure $lastBatchClosure
 * @property Collector $collector
 * @property Ecopago $ecopago
 * @property Payouts[] $payouts
 */
class BatchClosure extends ActiveRecord implements CountableInterface {

    //Scenarios
    const SCENARIO_FRONTEND = 'frontend';
    const SCENARIO_RENDER = 'render';
    //Status
    const STATUS_COLLECTED = 'collected';
    const STATUS_RENDERED = 'rendered';
    const STATUS_CANCELED = 'canceled';
    const STATUS_IRRECOVERABLE = 'irrecoverable';
    const STATUS_DUPLICATE = 'duplicate';

    //Collector info for creation
    public $collector_number;
    public $collector_password;
    //Payouts for information
    public $firstPayout;
    public $lastPayout;
    public $tempPayouts;
    //Flags 
    public $hasPayments = false;
    //Helpers attrs
    public $netTotal = 0;

    public function behaviors() {
        return [
            'account' => [
                'class' => 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'batch_closure';
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
            [['last_batch_closure_id', 'ecopago_id', 'collector_id', 'datetime', 'payment_count'], 'integer'],
            [['number', 'first_payout_number', 'last_payout_number'], 'string', 'max' => 50],
            [['total', 'commission', 'discount', 'real_total', 'money_box_account_id', 'difference'], 'number'],
            [['date', 'time', 'collector', 'ecopago', 'observation'], 'safe'],
            [['ecopago_id', 'collector_id'], 'required'],
            [['date'], 'date'],
            [['real_total', 'money_box_account_id', 'difference'], 'required', 'on' => static::SCENARIO_RENDER],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'batch_closure_id' => EcopagosModule::t('app', 'Batch closure'),
            'last_batch_closure_id' => EcopagosModule::t('app', 'Last Batch closure'),
            'ecopago_id' => EcopagosModule::t('app', 'Ecopago'),
            'collector_id' => EcopagosModule::t('app', 'Collector'),
            'date' => EcopagosModule::t('app', 'Date'),
            'time' => EcopagosModule::t('app', 'Time'),
            'datetime' => EcopagosModule::t('app', 'Datetime'),
            'number' => EcopagosModule::t('app', 'Number'),
            'total' => EcopagosModule::t('app', 'Raw total'),
            'net_total' => EcopagosModule::t('app', 'Net total'),
            'payment_count' => EcopagosModule::t('app', 'Payment Count'),
            'first_payout_number' => EcopagosModule::t('app', 'First Payout Number'),
            'last_payout_number' => EcopagosModule::t('app', 'Last Payout Number'),
            'commission' => EcopagosModule::t('app', 'Commission'),
            'discount' => EcopagosModule::t('app', 'Discount'),
            'collector' => EcopagosModule::t('app', 'Collector'),
            'collector_number' => EcopagosModule::t('app', 'Collector number'),
            'collector_password' => EcopagosModule::t('app', 'Collector password'),
            'ecopago' => EcopagosModule::t('app', 'Ecopago'),
            'last_payout' => EcopagosModule::t('app', 'Last payout'),
            'first_payout' => EcopagosModule::t('app', 'First payout'),
            'real_total' => EcopagosModule::t('app', 'Real rendered amount'),
            'company_id' => EcopagosModule::t('app', 'Destiny bank'),
            'money_box_account_id' => EcopagosModule::t('app', 'Destination money box account'),
            'difference' => EcopagosModule::t('app', 'Difference'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMoneyBoxAccount() {
        return $this->hasOne(MoneyBoxAccount::className(), ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCollector() {
        return $this->hasOne(Collector::className(), ['collector_id' => 'collector_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEcopago() {
        return $this->hasOne(Ecopago::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
      public function getPayouts() {
      return $this->hasMany(Payout::className(), ['batch_closure_id' => 'batch_closure_id']);
      }
     */
    public function getPayouts() {
        //return $this->hasMany($userModel::className(), [$userPK => 'user_id'])->viaTable('notification', ['task_id' => 'task_id']);
        return $this->hasMany(Payout::className(), ['payout_id' => 'payout_id'])->viaTable('batch_closure_has_payout', ['batch_closure_id' => 'batch_closure_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLastBatchClosure() {
        return $this->hasOne(BatchClosure::className(), ['batch_closure_id' => 'last_batch_closure_id']);
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public static function staticFetchStatuses() {
        return [
            static::STATUS_COLLECTED => EcopagosModule::t('app', 'Collected'),
            static::STATUS_RENDERED => EcopagosModule::t('app', 'Rendered'),
            static::STATUS_CANCELED => EcopagosModule::t('app', 'Canceled'),
            static::STATUS_IRRECOVERABLE => EcopagosModule::t('app', 'Irrecuperable'),
            static::STATUS_DUPLICATE => EcopagosModule::t('app', 'Duplicado'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public function fetchStatuses() {
        return [
            static::STATUS_COLLECTED => EcopagosModule::t('app', 'Collected'),
            static::STATUS_RENDERED => EcopagosModule::t('app', 'Rendered'),
            static::STATUS_CANCELED => EcopagosModule::t('app', 'Canceled'),
            static::STATUS_IRRECOVERABLE => EcopagosModule::t('app', 'Irrecuperable'),
            static::STATUS_DUPLICATE => EcopagosModule::t('app', 'Duplicado'),
        ];
    }

    /**
     * Sets several attributes of this batchClosure instance to give more information about this pending batch closure operation
     */
    public function preview() {

        //Fetch last batch closure on this ecopago branch
        $previusBatchClosure = BatchClosure::find()->where([
                    'ecopago_id' => $this->ecopago_id
                ])->orderBy([
                    'datetime' => SORT_DESC
                ])->one();

        if (!empty($previusBatchClosure))
            $this->last_batch_closure_id = $previusBatchClosure->batch_closure_id;

        $payoutSearch = new PayoutSearch();
        $payoutQuery = $payoutSearch->queryFindByBatchClosure($this);
        $payouts = $payoutQuery->all();

        //If there is payments to be closed
        if (!empty($payouts)) {
            $this->hasPayments = true;
            $this->payment_count = count($payouts);
            $this->total = $payoutQuery->sum('amount');
            $this->calculateCommission();

            //Calculate net total
            $this->netTotal = $this->total - $this->commission;

            $this->first_payout_number = (string) end($payouts)->payout_id;
            $this->firstPayout = end($payouts);
            $this->last_payout_number = (string) $payouts[0]->payout_id;
            $this->lastPayout = $payouts[0];

            $this->tempPayouts = $payouts;
        }

        //If there is not payments to be closed
        else {
            $this->hasPayments = false;
        }
    }

    /**
     * Makes an attempt to execute this batch closure
     * @return boolean
     */
    public function execute() {

        $this->scenario = BatchClosure::SCENARIO_DEFAULT;

        //If everything is OK, execute this batch closure
        if ($this->hasPayments && !empty($this->collector_id) && !empty($this->tempPayouts)) {

            //Set status to 'collected'
            $this->status = static::STATUS_COLLECTED;

            //Save instance
            if (!$this->save()) {
                return false;
            }

            foreach ($this->tempPayouts as $payout) {

                //Sets the batch closure to this payout and then closes it
                $payout->scenario = Payout::SCENARIO_BATCHCLOSE;
                $payout->batch_closure_id = $this->batch_closure_id;
                $this->link('payouts', $payout);
                if (!$payout->close()){
                    \app\components\helpers\FlashHelper::flashErrors($payout);
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Makes an attempt to cancel this batch closure
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
     * @inheritdoc
     * Strong relations: Payouts
     */
    public function getDeletable() {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }
        return false;
    }

    /**
     * Checks if a specific batch closure is cancelable or not
     */
    public function isCancelable() {

        //A batch closure cannot be cancelled
        return false;

        if ($this->status == static::STATUS_RENDERED || $this->status == static::STATUS_CANCELED)
            return false;

        return true;
    }

    /**
     * Makes an attempt to render this batch closure
     * @return boolean
     */
    public function render() {

        //If this batch closure is renderable
        if ($this->isRenderable()) {

            if (empty($this->real_total) || $this->real_total < 0) {
                $this->addError('real_total', EcopagosModule::t('app', 'Real total must be completed'));
                return false;
            }

            if (empty($this->money_box_account_id) || $this->money_box_account_id < 0) {
                $this->addError('money_box_account_id', EcopagosModule::t('app', 'Money box account must be completed'));
                return false;
            }

            $oldstatus = $this->status;
            //Change the status to rendered
            $this->status = static::STATUS_RENDERED;

            //Try to save it
            if ($this->save()) {
                $bcService = BatchClosureService::getInstance();

                // Genero comprobantes
                if (!$bcService->registerBill($this) ) {
                    foreach ( $bcService->messages as $key=>$message ) {
                        Yii::$app->session->addFlash('error', EcopagosModule::t('app', $message));
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a specific batch closure is renderable or not
     * @return boolean
     */
    public function isRenderable() {

        //If the ecopago branch where this batch closure is being executed not has an account, it cannot execute batch closures
        if (empty($this->ecopago->account))
            return false;

        if ($this->status == static::STATUS_COLLECTED)
            return true;

        return false;
    }

    /**
     * Calculates the commission for this batch closure
     */
    public function calculateCommission() {

        if (!empty($this->total) && !empty($this->ecopago_id)) {

            $commission = $this->ecopago->getActiveCommission();

            switch ($commission->type) {

                //By percentage
                case Commission::COMMISSION_TYPE_PERCENTAGE :
                    $this->commission = $this->total * ($commission->value / 100);
                    break;

                //By total
                case Commission::COMMISSION_TYPE_FIXED :
                    $this->commission = $commission->value;
                    break;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            //If it is a new instance
            if ($insert) {
                $this->date = date('Y-m-d');
                $this->time = date('H:i:s');
                $this->datetime = time();
            }

            //If it is an old instance
            else {
                
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

        parent::afterFind();

        //Calculate net total
        $this->netTotal = $this->total - $this->commission;

        //Set first and last payout
        if (!empty($this->payouts)) {
            $payouts = $this->payouts;
            $this->firstPayout = $payouts[0];
            $this->lastPayout = end($payouts);
        }

        //If its used from frontEnd, each batch closure must be associated to an ecopago branch
        if ($this->scenario == BatchClosure::SCENARIO_FRONTEND)
            $this->ecopago_id = UserHelper::getEcopago()->ecopago_id;

        $this->formatDatesAfterFind();
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
     * Deletes weak relations for this model on delete
     */
    protected function unlinkWeakRelations() {
        //reopen each payout for another batch closure can close them
        $this->reopenPayouts();
        AccountMovementRelationManager::delete($this);
    }

    /**
     * Reopens each of this batch closure payouts before deleting the closure
     */
    private function reopenPayouts() {

        if (!empty($this->payouts)) {
            foreach ($this->payouts as $payout) {
                $payout->reopen();
            }
        }
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

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return ['total'];
    }

    /**
     * @inheritdoc
     */
    public function getAmounts() {
        return ['total'=>$this->real_total];
    }

    /**
     * Builds a print layout for printer
     * @return string
     */
    public function getPrintLayout() {
        $buffer = [];

        $printer = new EscPrinter();

        $printer->writeText("********** WestNet - Ecopagos **********");
        $printer->feed();

        $printer->writeText("Cierre de lote " . $this->batch_closure_id);
        $printer->feed();

        $printer->writeText("Sucursal: " . $this->ecopago->name);
        $printer->feed();

        $printer->writeText("Cobrador: " . UserHelper::getCashier()->getCompleteName());
        $printer->feed(2);

        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();


        $printer->writeText("Fecha: " . date("H:i", $this->datetime) . 'hs ' . date("d/m/Y", $this->datetime));
        $printer->feed();

        $printer->writeText("Recaudador " . $this->collector->getFormattedName() );
        $printer->feed();


        $printer->writeText("Numero primer ticket de pago: " . $this->first_payout_number );
        $printer->feed();

        $printer->writeText("Numero ultimo ticket de pago: " . $this->last_payout_number );
        $printer->feed();

        $buffer[] = $printer->getBuffer();
        $printer->clearBuffer();

        $printer->writeText("Cantidad de pagos: " . $this->payment_count );
        $printer->feed();

        $printer->writeText("Monto total: $" . $this->total );
        $printer->feed();

        $printer->writeText("Comision: $" . $this->commission );
        $printer->feed(10);

        $buffer[] = $printer->getBuffer();

        return json_encode($buffer);
    }

}
