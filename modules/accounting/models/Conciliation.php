<?php

namespace app\modules\accounting\models;

use app\components\workflow\WithWorkflow;
use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\search\AccountMovementSearch;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "conciliation".
 *
 * @property integer $conciliation_id
 * @property string $name
 * @property string $date
 * @property string $date_from
 * @property string $date_to
 * @property string $status
 * @property integer $timestamp
 * @property integer $money_box_account_id
 * @property integer $resume_id
 *
 * @property ConciliationItem[] $conciliationItems
 * @property MoneyBoxAccount $moneyBoxAccount
 * @property Resume $resume
 */
class Conciliation extends \app\components\companies\ActiveRecord
{

    public $money_box_id;
    use WithWorkflow;

    const STATE_DRAFT       = 'draft';
    const STATE_CANCELED    = 'canceled';
    const STATE_CLOSED      = 'closed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conciliation';
    }
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $statuses = ['draft','closed'];

        $rules = [
            [['name', 'money_box_account_id', 'date_from', 'date_to', 'resume_id'], 'required'],
            [['resume_id'], 'integer'],
            [['date', 'date_from', 'date_to', 'money_box_id'], 'safe'],
            [['date', 'date_from', 'date_to'], 'date'],
            [['status'], 'in', 'range' => $statuses],
            [['status'], 'default', 'value'=>'draft'],
            [['timestamp'], 'integer'],
            [['name'], 'string', 'max' => 150]
        ];

        if (Yii::$app->params['companies']['enabled']) {
            $rules[] = [['company_id'], 'required'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'conciliation_id' => Yii::t('accounting', 'Conciliation ID'),
            'name' => Yii::t('app', 'Name'),
            'date' => Yii::t('app', 'Date'),
            'date_from' => Yii::t('accounting', 'Date From'),
            'date_to' => Yii::t('accounting', 'Date To'),
            'status' => Yii::t('app', 'Status'),
            'timestamp' => Yii::t('accounting', 'Timestamp'),
            'conciliationItems' => Yii::t('accounting', 'ConciliationItems'),
            'Resume' => Yii::t('accounting', 'Resume'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::className(), ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliationItems()
    {
        return $this->hasMany(ConciliationItem::className(), ['conciliation_id' => 'conciliation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResume()
    {
        return $this->hasOne(Resume::className(), ['resume_id' => 'resume_id']);
    }


    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();            
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
            $this->date_from = Yii::$app->formatter->asDate($this->date_from);
            $this->date_to = Yii::$app->formatter->asDate($this->date_to);
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
            $this->date_from = Yii::$app->formatter->asDate($this->date_from, 'yyyy-MM-dd');
            $this->date_to = Yii::$app->formatter->asDate($this->date_to, 'yyyy-MM-dd');
        }
    
         
    /**
     * @inheritdoc
     * Strong relations: ConciliationItems.
     */
    public function getDeletable()
    {
        if($this->getConciliationItems()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations(){
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
     * Retorna el total de debito y credito.
     *
     * @return int|mixed
     */
    public function getTotals()
    {
        $credit = 0;
        $debit = 0;

        foreach($this->getConciliationItems()->all() as $item) {
            foreach($item->getConciliationItemHasResumeItems()->all() as $res) {
                $credit += ($res->resumeItem->credit > 0 ? $res->resumeItem->credit : 0 );
                $debit  += ($res->resumeItem->debit > 0 ? $res->resumeItem->debit : 0 );
            }
        }

        return [
            'debit' => $debit,
            'credit' => $credit
        ];
    }


    /**
     * Cierra una conciliacion, marcando los items como coniciliados y generando
     * los
     *
     * @return bool
     */
    public function close()
    {
        $bOk = false;
        if($this->can(self::STATE_CLOSED) && $this->getConciliationItems()->count() > 0 ) {
            /** @var ConciliationItem $item */
            foreach($this->getConciliationItems()->all() as $item) {
                $resumeItems = $item->getConciliationItemHasResumeItems()->all();
                $accountItems = $item->getConciliationItemHasAccountMovementItems()->all();
                // Si no tiene movimientos contable los creo
                if (count($accountItems) == 0) {
                    $debit = 0;
                    $credit = 0;

                    $items = [];
                    /** @var ConciliationItemHasResumeItem $res */
                    foreach( $resumeItems as $res) {
                        // Obtengo el tipo de operacion
                        $opType = $res->resumeItem->moneyBoxHasOperationType;
                        if($opType) {
                            if ($opType->account) {
                                $mov = new AccountMovementItem();
                                $mov->account_id = $opType->account->account_id;
                                $mov->status = AccountMovementItem::STATE_CONCILED;
                                if($opType->operationType->is_debit) {
                                    $mov->debit = $res->resumeItem->debit;
                                } else {
                                    $mov->credit = $res->resumeItem->credit;
                                }
                                $debit += $mov->debit;
                                $credit += $mov->credit;
                                $res->resumeItem->changeState(ResumeItem::STATE_CONCILED);
                                $items[] = $mov;
                                $bOk = true;
                            } else {
                                $bOk = false;
                                break;
                            }
                        } else {
                            $bOk = false;
                            break;
                        }
                    }

                    // Creo el movimiento contable
                    if ($bOk) {
                        // creo los movimientos del banco
                        if ($debit>0) {
                            $mov = new AccountMovementItem();
                            $mov->account_id = $this->moneyBoxAccount->account->account_id;
                            $mov->status = AccountMovementItem::STATE_CONCILED;
                            $mov->credit = $debit;
                            $items[] = $mov;
                        }

                        // creo los movimientos del banco
                        if ($credit>0) {
                            $mov = new AccountMovementItem();
                            $mov->account_id = $this->moneyBoxAccount->account->account_id;
                            $mov->status = AccountMovementItem::STATE_CONCILED;
                            $mov->debit = $credit;
                            $items[] = $mov;
                        }
                        $countMov = new CountableMovement();
                        $countMov->createMovement(Yii::t('accounting','Conciliation') . " - " .$this->name, $this->company_id, $items);
                    }
                } else {
                    // Como tiene movimientos modifico el estado
                    foreach( $resumeItems as $res) {
                        $res->resumeItem->changeState(ResumeItem::STATE_CONCILED);
                    }
                    foreach( $accountItems as $res) {
                        $res->accountMovementItem->changeState(AccountMovementItem::STATE_CONCILED);
                    }
                    $bOk = true;
                }
            }
            if ($bOk) {
                $this->changeState(self::STATE_CLOSED);
            }
        } else {
            throw new \Exception('The conciliation can\'t be closed or don\'t have items to conciliate.');
        }
        return $bOk;
    }

    /**
     * Retorna el atributo que maneja el estado del objeto para el workflow.
     *
     * @return mixed
     */
    public function getWorkflowAttr()
    {
        return "status";
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function getWorkflowStates()
    {
        return [
            self::STATE_DRAFT => [
                self::STATE_CLOSED
            ],
            self::STATE_CLOSED => [
                self::STATE_CANCELED
            ],
            self::STATE_CANCELED => [
                self::STATE_CLOSED
            ]
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}

    /*
     * Verifica que el saldo de la cuenta es igual al saldo final del resumen
     */
    public function validateBalance()
    {
        $searchModel = new AccountMovementSearch();
        $searchModel->account_id_from = $this->moneyBoxAccount->account->lft;
        $searchModel->account_id_to = $this->moneyBoxAccount->account->rgt;
        $searchModel->fromDate = $this->date_from;
        $searchModel->toDate = $this->date_to;
        $searchModel->balance = 'credit';

        $creditDataProvider = $searchModel->searchForConciliation([]);

        $totalAccountCredit = $searchModel->totalCredit;

        $searchModel->balance = 'debit';
        $debitDataProvider = $searchModel->searchForConciliation([]);
        $totalAccountDebit = $searchModel->totalDebit;

        $balance = $totalAccountCredit - $totalAccountDebit;

        if ($this->resume->balance_final !== $balance) {
            return false;
        }

        return true;


    }
}
