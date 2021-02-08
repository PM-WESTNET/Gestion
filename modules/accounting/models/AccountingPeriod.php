<?php

namespace app\modules\accounting\models;

use app\components\workflow\WithWorkflow;
use Yii;
use yii\base\UserException;
use yii\web\HttpException;

/**
 * This is the model class for table "accounting_period".
 *
 * @property integer $accounting_period_id
 * @property string $name
 * @property string $date_from
 * @property string $date_to
 * @property integer $number
 * @property string $status
 * @property integer $conciliation_conciliation_id
 *
 */
class AccountingPeriod extends \app\components\db\ActiveRecord
{
    use WithWorkflow;

    const STATE_OPEN        = 'open';
    const STATE_CLOSED      = 'closed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'accounting_period';
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
            [['name'], 'required'],
            [['date_from', 'date_to'], 'safe'],
            [['date_from', 'date_to'], 'date'],
            [['number'], 'integer'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 150],
            [['status'], 'in', 'range' => ['open', 'closed']],
            [['status'], 'default', 'value'=>'closed'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'accounting_period_id' => Yii::t('accounting', 'Accounting Period ID'),
            'name' => Yii::t('accounting', 'Name'),
            'date_from' => Yii::t('accounting', 'Date From'),
            'date_to' => Yii::t('accounting', 'Date To'),
            'number' => Yii::t('accounting', 'Number'),
            'status' => Yii::t('app', 'Status'),
        ];
    }    


    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();
            if($this->status == AccountingPeriod::STATE_OPEN){
                if ($this->accounting_period_id) {
                    AccountingPeriod::updateAll(['status' => AccountingPeriod::STATE_CLOSED], 'accounting_period_id <>'.$this->accounting_period_id );
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
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
            $this->date_from = Yii::$app->formatter->asDate($this->date_from);
            $this->date_to = Yii::$app->formatter->asDate($this->date_to);
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->date_from = Yii::$app->formatter->asDate($this->date_from, 'yyyy-MM-dd');
            $this->date_to = Yii::$app->formatter->asDate($this->date_to, 'yyyy-MM-dd');
    }
    
         
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->status == AccountingPeriod::STATE_OPEN && AccountMovement::find()->where(['accounting_period_id'=>$this->accounting_period_id])->count()==0);
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: ConciliationConciliation.
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
     * Retorna el Periodo activo.
     *
     * @return null|static
     */
    public static function getActivePeriod()
    {
        $period = AccountingPeriod::findOne(['status'=>AccountingPeriod::STATE_OPEN]);

        if(!$period){
            throw new UserException(Yii::t('accounting','No accounting period opened. Create a new Period or Open one.'));
        }
        return $period;
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
            self::STATE_OPEN => [
                self::STATE_CLOSED
            ],
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}

    /**
     * Close all movements
     */
    public function close()
    {
        $transaction = $this->db->beginTransaction();
        try{

            AccountMovement::updateAll(['status'=>AccountMovement::STATE_CLOSED], ['accounting_period_id'=> $this->accounting_period_id]);

            $this->updateAttributes(['status'=>AccountingPeriod::STATE_CLOSED]);

            $transaction->commit();
            return true;
        } catch(\Exception $ex) {
            $transaction->rollback();
        }
        return false;
    }
}
