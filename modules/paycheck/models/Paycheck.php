<?php

namespace app\modules\paycheck\models;

use app\modules\accounting\components\CountableInterface;
use app\modules\accounting\models\MoneyBox;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\modules\provider\models\ProviderPayment;
use app\modules\provider\models\ProviderPaymentItem;
use Yii;
use app\modules\accounting\components\AccountMovementRelationManager;

/**
 * This is the model class for table "paycheck".
 *
 * @property integer $paycheck_id
 * @property string $date
 * @property string $due_date
 * @property string $number
 * @property double $amount
 * @property string $document_number
 * @property string $status
 * @property string $business_name
 * @property string $description
 * @property integer $is_own
 * @property integer $timestamp
 * @property integer $checkbook_id
 * @property integer $money_box_id
 * @property integer $crossed
 * @property integer $to_order
 * @property integer $money_box_account_id
 *
 * @property Checkbook $checkbook
 * @property MoneyBox $moneyBox
 * @property PaycheckLog[] $paycheckLogs
 * @property PaymentItem[] $paymentItems
 * @property ProviderPaymentItem[] $providerPaymentItems
 * @property MoneyBoxAccount $moneyBoxAccount
 */
class Paycheck extends \app\components\db\ActiveRecord implements CountableInterface
{
    const STATE_CREATED     = 'created';
    const STATE_COMMITED    = 'commited';
    const STATE_RECEIVED    = 'received';
    const STATE_CANCELED    = 'canceled';
    const STATE_CASHED      = 'cashed';
    const STATE_REJECTED    = 'rejected';
    const STATE_RETURNED    = 'returned';
    const STATE_DEPOSITED   = 'deposited';

    public $states = [];

    public $dateStamp;

    public $outAccount;

    protected $all_statuses = [
        'created',
        'commited',
        'received',
        'canceled',
        'cashed',
        'rejected',
        'returned',
        'deposited',
    ];

    public function init()
    {
        parent::init();

        $this->states = [
            'own' => [
                self::STATE_CREATED => [
                    self::STATE_COMMITED,
                    self::STATE_CANCELED,
                    self::STATE_DEPOSITED
                ],
                self::STATE_COMMITED => [
                    self::STATE_CASHED,
                    self::STATE_REJECTED,
                    self::STATE_RETURNED
                ],
                self::STATE_REJECTED => [
                    self::STATE_DEPOSITED,
                    self::STATE_CANCELED,
                ],
                self::STATE_RETURNED => [
                    self::STATE_CANCELED,
                    self::STATE_COMMITED,
                    self::STATE_DEPOSITED,
                ],
                self::STATE_DEPOSITED => [
                    self::STATE_REJECTED,
                ],
            ],
            'no_own' => [
                self::STATE_RECEIVED => [
                    self::STATE_COMMITED,
                    self::STATE_DEPOSITED,
                    self::STATE_RETURNED,
                    self::STATE_CASHED,
                ],
                self::STATE_COMMITED => [
                    self::STATE_RETURNED
                ],
                self::STATE_DEPOSITED => [
                    self::STATE_REJECTED
                ],
                self::STATE_RETURNED => [
                    self::STATE_CANCELED,
                    self::STATE_COMMITED,
                    self::STATE_DEPOSITED,
                    self::STATE_RETURNED,
                    self::STATE_CASHED,
                ],
                self::STATE_REJECTED=> [
                    self::STATE_RETURNED,
                    self::STATE_DEPOSITED,
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paycheck';
    }
    
    /**
     * @inheritdoc
     */
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

        return [
            [['date', 'due_date', 'timestamp', 'checkbook', 'moneyBox', 'moneyBoxAccount', 'dateStamp', 'outAccount'], 'safe'],
            [['date', 'due_date', 'dateStamp'], 'date', 'min'=>'01-01-1980', 'format'=>'dd-MM-yyyy'],
            [['date'], 'default', 'value'=> null],
            [['amount'], 'number'],
            [['money_box_id','number', 'amount', 'date', 'due_date'], 'required'],
            [['is_own', 'timestamp', 'checkbook_id', 'money_box_id', 'crossed', 'to_order', 'money_box_account_id'], 'integer'],
            [['number', 'document_number', 'status'], 'string', 'max' => 45],
            [['business_name', 'description'], 'string', 'max' => 255],
            ['checkbook_id', 'required', 'when' => function () {
                    return $this->is_own;
                }, 'whenClient' => "function (attribute, value) {
                    return $('#paycheck-is_own:checked').val() == 1;
            }"],
            [['status'], 'in', 'range' => $this->all_statuses],
            //[['money_box_account_id'], 'required', 'when'=>function(){ return ($this->status == self::STATE_DEPOSITED); }],
            ['number','compare', 'compareValue' => 0, 'operator' => '>='],
            ['number','unique', 'targetAttribute' => ['checkbook_id','number'], 'when'=>function(){return $this->isNewRecord;}],
            ['number', 'validateCheeckbookRange'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'paycheck_id' => Yii::t('paycheck', 'Paycheck ID'),
            'date' => Yii::t('app', 'Date'),
            'due_date' => Yii::t('paycheck', 'Due Date'),
            'number' => Yii::t('app', 'Number'),
            'amount' => Yii::t('app', 'Amount'),
            'document_number' => Yii::t('paycheck', 'Document Number'),
            'status' => Yii::t('paycheck', 'Status'),
            'business_name' => Yii::t('app', 'Issuer Business Name'),
            'description' => Yii::t('app', 'Description'),
            'is_own' => Yii::t('paycheck', 'Is Own'),
            'timestamp' => Yii::t('paycheck', 'Timestamp'),
            'checkbook_id' => Yii::t('paycheck', 'Checkbook'),
            'money_box_id' => Yii::t('paycheck', 'Money Box'),
            'money_box_account_id' => Yii::t('paycheck', 'Money Box Account'),
            'checkbook' => Yii::t('paycheck', 'Checkbook'),
            'moneyBox' => Yii::t('paycheck', 'Money Box'),
            'paycheckLogs' => Yii::t('paycheck', 'PaycheckLogs'),
            'payments' => Yii::t('paycheck', 'Payments'),
            'providerPayments' => Yii::t('paycheck', 'ProviderPayments'),
            'crossed' => Yii::t('paycheck', 'Crossed'),
            'to_order' => Yii::t('paycheck', 'To Order'),
            'Owned' => 'Owned'
        ];
    }

    public function validateCheeckbookRange($attribute, $params, $validator)
    {
        if($this->is_own) {
            $cheeckbook = Checkbook::findOne($this->checkbook_id);
            if($this->number < $cheeckbook->start_number || $this->number > $cheeckbook->end_number){
                $this->addError($attribute, Yii::t('app','The number is out of cheeckbook´s range'));
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckbook()
    {
        return $this->hasOne(Checkbook::className(), ['checkbook_id' => 'checkbook_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBox()
    {
        return $this->hasOne(MoneyBox::className(), ['money_box_id' => 'money_box_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaycheckLogs()
    {
        return $this->hasMany(PaycheckLog::className(), ['paycheck_id' => 'paycheck_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentItems()
    {
        return $this->hasMany(PaymentItem::className(), ['paycheck_id' => 'paycheck_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderPaymentsItems()
    {
        return $this->hasMany(ProviderPaymentItem::className(), ['paycheck_id' => 'paycheck_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::className(), ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();
            if ($this->is_own) {
                if ($this->isNewRecord) {
                    $this->status = self::STATE_CREATED;
                }
            } else {
                if ($this->isNewRecord) {
                    $this->status = self::STATE_RECEIVED;
                }
            }
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
        if(!$this->isNewRecord) {
        }

        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
        $this->due_date = Yii::$app->formatter->asDate($this->due_date);
    }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        $this->due_date = Yii::$app->formatter->asDate($this->due_date, 'yyyy-MM-dd');
        if($this->dateStamp) {
            $date =  (new \DateTime(Yii::$app->formatter->asDate($this->dateStamp, 'yyyy-MM-dd')));
            $this->timestamp = (new \DateTime($date->format('Y-m-d'). (new \DateTime("now"))->format(" H:i:s")))->getTimestamp();
        } else {
            $this->timestamp = (new \DateTime("now"))->getTimestamp();
        }
    }


    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        if (($this->getProviderPaymentsItems()->count() === 0) && ($this->getPaymentItems()->count() === 0)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     * Verifica si el cheque se puede actualizar.
     */
    public function getUpdatable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        if ($this->status != Paycheck::STATE_CREATED && $this->status != Paycheck::STATE_RECEIVED) {
            return false;
        }

        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Checkbook, MoneyBox, PaycheckLogs, Payments, ProviderPayments.
     */
    protected function unlinkWeakRelations(){
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

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        // Si estoy y es un cheque propio actualizo la chequera
        if ($insert && $this->is_own) {
            $this->checkbook->last_used = $this->checkbook->lastNumberUsed + 1;
            $this->checkbook->save();
        }
    }

    /**
     * Retorna los posibles estados del cheque.
     *
     * @return mixed
     */
    public function getPossibleStates()
    {
        $states = $this->states[($this->is_own?'own':'no_own')];
        if (array_key_exists($this->status, $states)) {
            return $states[$this->status];
        } else {
            $log = PaycheckLog::find()
                ->where(['paycheck_id'=>$this->paycheck_id])
                ->orderBy('paycheck_log_id', SORT_DESC)
                ->one();
            if ($log) {
                return [$log->status];
            }
            return [];
        }
    }

    public function can($state)
    {
        $possible = $this->getPossibleStates();
        if (is_array($possible)) {
            return array_search($state, $possible)!==false;
        } else {
            return ($state == $possible);
        }
    }


    /**
     * Retorna la descripcion completa del cheque.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getFullDescription()
    {
        return ($this->is_own ?
            Yii::t('paycheck', 'own') . " - " . $this->checkbook->moneyBoxAccount->moneyBox->name . " - " . $this->checkbook->moneyBoxAccount->number
            :
            Yii::t('paycheck', 'no_own') . " - " . $this->moneyBox->name
        ) . " - " . Yii::t('paycheck', 'number') .": " . $this->number . " - " .
        Yii::t('paycheck', 'Due Date') . ": " . Yii::$app->formatter->asDate($this->due_date) . " - " .
        Yii::$app->formatter->asCurrency($this->amount);

    }

    public function createLog()
    {
        $log = new PaycheckLog();
        $log->paycheck_id = $this->paycheck_id;
        $log->timestamp = $this->timestamp;
        $log->status = $this->status;
        $log->description = $this->description;
        $log->money_box_account_id = $this->money_box_account_id;
        $log->save();
    }

    /**
     * Revierte el estado al estado anterior del cheque.
     */
    public function revertState()
    {
        $lastState = PaycheckLog::find()
            ->where(['paycheck_id'=>$this->paycheck_id])
            ->orderBy('paycheck_log_id', SORT_DESC)
            ->one();
        if ($lastState) {
            $this->createLog();
            $this->status = $lastState->status;
            $this->description = $lastState->description;
            $this->timestamp = $lastState->timestamp;
            $this->money_box_account_id = $lastState->money_box_account_id;
            $this->save(false);
        }
    }

    public function changeState($state)
    {
        $this->createLog();
        $this->status = $state;
        return $this->save(false);
    }

    public function getConfig()
    {
        return [
            'total' => 'Total'
        ];
    }

    public function getAmounts()
    {
        return [
            'total' => $this->amount
        ];
    }
}
