<?php

namespace app\modules\accounting\models;

use app\components\workflow\WithWorkflow;
use Yii;

/**
 * This is the model class for table "resume_item".
 *
 * @property integer $resume_item_id
 * @property integer $resume_id
 * @property string $description
 * @property string $reference
 * @property string $code
 * @property double $debit
 * @property double $credit
 * @property string $status
 * @property string $date
 * @property integer $money_box_has_operation_type_id
 * @property boolean $ready
 *
 * @property ConciliationItem[] $conciliationItems
 * @property MoneyBoxHasOperationType $moneyBoxHasOperationType
 * @property Resume $resume
 */
class ResumeItem extends \app\components\db\ActiveRecord
{

    use WithWorkflow;

    const STATE_DRAFT       = 'draft';
    const STATE_CONCILED    = 'conciled';
    const STATE_CLOSED      = 'closed';

    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resume_item';
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
        return [
            [['resume_id', 'date', 'money_box_has_operation_type_id'], 'required'],
            [['resume_id', 'money_box_has_operation_type_id'], 'integer'],
            [['debit', 'credit'], 'double'],
            [['debit', 'credit', 'ready'], 'default', 'value'=> 0],
            [['status'], 'in', 'range' => ['draft', 'conciled']],
            [['status'], 'default', 'value'=>'draft'],
            [['date', 'moneyBoxHasOperationType', 'resume'], 'safe'],
            [['money_box_has_operation_type_id'], 'required'],
            [['date'], 'date'],
            [['description'], 'string', 'max' => 150],
            [['reference', 'code'], 'string', 'max' => 45],
            [['date'], 'validateDate'],
            [['debit', 'credit'], 'validateAmount'],
            [['ready'], 'boolean']
            /*['debit', 'required', 'when' => function($model) {
                return ($model->moneyBoxHasOperationType->getOperationType()->one()->is_debit && $model->debit == 0);
            }],
            ['credit', 'required', 'when' => function($model) {
                return (!$model->moneyBoxHasOperationType->operationType->is_debit && $model->credit == 0);
            }],*/
            
        ];
    }

    /**
     * Valido credito y debito
     * @param $attribute
     * @param $params
     */
    public function validateAmount($attribute,$params)
    {
        if($this->moneyBoxHasOperationType->operationType && $attribute == 'debit') {
            if($this->moneyBoxHasOperationType->operationType->is_debit && $this->debit === 0) {
                $this->addError($attribute, Yii::t('accounting', 'The amount must be greater than 0.'));
            }
        }
        if($this->moneyBoxHasOperationType->operationType && $attribute == 'credit') {
            if(!$this->moneyBoxHasOperationType->operationType->is_debit && $this->credit === 0) {
                $this->addError($attribute, Yii::t('accounting', 'The amount must be greater than 0.'));
            }
        }
    }

    /**
     * Valido las fechas del resumen
     * @param $attribute
     * @param $params
     */
    public function validateDate($attribute,$params)
    {
        $date = new \DateTime($this->date);
        $dateFrom = new \DateTime($this->resume->date_from);
        $dateTo = new \DateTime($this->resume->date_to);
        if ($date < $dateFrom || $date > $dateTo) {
            $this->addError($attribute, Yii::t('accounting', 'The date can not be greater or less than the range of the resume.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resume_item_id' => Yii::t('accounting', 'Resume Item ID'),
            'resume_id' => Yii::t('accounting', 'Resume ID'),
            'money_box_has_operation_type_id' => Yii::t('accounting', 'Operation Type'),
            'description' => Yii::t('app', 'Description'),
            'reference' => Yii::t('accounting', 'Reference'),
            'code' => Yii::t('app', 'Code'),
            'debit' => Yii::t('accounting', 'Debit'),
            'credit' => Yii::t('accounting', 'Credit'),
            'status' => Yii::t('app', 'Status'),
            'date' => Yii::t('app', 'Date'),
            'conciliationItems' => Yii::t('accounting', 'ConciliationItems'),
            'resume' => Yii::t('accounting', 'Resume'),
            
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliationItems()
    {
        return $this->hasMany(ConciliationItem::className(), ['resume_item_id' => 'resume_item_id']);
    }

    /**
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxHasOperationType()
    {
        return $this->hasOne(MoneyBoxHasOperationType::className(), ['money_box_has_operation_type_id' => 'money_box_has_operation_type_id']);
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
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
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
     * Weak relations: ConciliationItems, OperationType, Resume.
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
     * Retorna el atributo que maneja el estado del objeto para el workflow.
     *
     * @return mixed
     */
    public function getWorkflowAttr()
    {
        return "status";
    }

    /**
     * Retorna los estados.
     *
     * @return mixed
     */
    public function getWorkflowStates()
    {
        return [
            self::STATE_DRAFT => [
                self::STATE_CLOSED,
                self::STATE_CONCILED
            ],
            self::STATE_CONCILED => [
                self::STATE_CLOSED,
            ],
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}
}
