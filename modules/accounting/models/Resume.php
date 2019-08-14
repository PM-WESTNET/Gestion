<?php

namespace app\modules\accounting\models;

use app\components\workflow\WithWorkflow;
use Yii;

/**
 * This is the model class for table "resume".
 *
 * @property integer $resume_id
 * @property integer $money_box_account_id
 * @property string $date
 * @property string $date_from
 * @property string $date_to
 * @property string $status
 * @property string $name
 * @property double $balance_initial
 * @property double $balance_final
 *
 * @property MoneyBoxAccount $moneyBoxAccount
 * @property ResumeItem[] $resumeItems
 */
class Resume extends \app\components\companies\ActiveRecord
{
    use WithWorkflow;

    public $money_box_id;
    public $file_import;
    public $columns;
    public $account_id;
    public $separator;

    const STATE_DRAFT       = 'draft';
    const STATE_CANCELED    = 'canceled';
    const STATE_CLOSED      = 'closed';
    const STATE_CONCILED    = 'conciled';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resume';
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
        $rules = [
            [['money_box_account_id', 'name'], 'required'],
            [['money_box_account_id'], 'integer'],
            [['date', 'date_from', 'date_to', 'moneyBoxAccount', 'company_id', 'columns', 'account_id', 'separator'], 'safe'],
            [['date', 'date_from', 'date_to'], 'date'],
            [['status'], 'in', 'range' => ['draft', 'closed', 'canceled']],
            [['status'], 'default', 'value'=>'draft'],
            [['balance_initial', 'balance_final'], 'number'],
            [['file_import'], 'file', 'extensions' => 'csv'],
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
            'resume_id' => Yii::t('accounting', 'Resume ID'),
            'money_box_account_id' => Yii::t('accounting', 'Money Box Account ID'),
            'date' => Yii::t('app', 'Date'),
            'date_from' => Yii::t('accounting', 'Date From'),
            'date_to' => Yii::t('accounting', 'Date To'),
            'status' => Yii::t('app', 'Status'),
            'moneyBoxAccount' => Yii::t('accounting', 'MoneyBoxAccount'),
            'resumeItems' => Yii::t('accounting', 'ResumeItems'),
            'name' => Yii::t('app', 'Name'),
            'balance_initial' => Yii::t('accounting', 'Initial Balance'),
            'balance_final' => Yii::t('accounting', 'Final Balance'),
            'file_import' => Yii::t('accounting', 'File To Import'),
            'account_id' => Yii::t('accounting','Account'),
            'columns' => Yii::t('accounting','Columns'),
            'separator' => Yii::t('accounting','Separator'),
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
    public function getResumeItems()
    {
        return $this->hasMany(ResumeItem::className(), ['resume_id' => 'resume_id']);
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
     * Strong relations: ResumeItems.
     */
    public function getDeletable()
    {
        if($this->getResumeItems()->exists() || $this->status == 'closed'){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MoneyBoxAccount.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('resumeItems');
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
     * Retorna los resumenes de cuenta de una cuenta bancaria en particular y que
     * se encuentren cerradas, listas para ser conciliadas.
     *
     * @param $money_box_account_id
     * @return $this
     */
    public static function findResumeByAccount($money_box_account_id)
    {
        return Resume::find()
            ->select(['resume_id as id', 'name'])
            ->andFilterWhere(['money_box_account_id' => $money_box_account_id])
            ->andFilterWhere(['status' => 'closed']);
    }

    /**
     * Retorna todos los items disponibles de un resumen para ser conciliados.
     *
     * @param $resume_id
     */
    public function getResumeItemsEnabled($resume_id)
    {
        $query = ResumeItem::find()
            ->leftJoin('conciliation_item_has_resume_item ciri', 'ciri.resume_item_id =  resume_item.resume_item_id ')
            ->andWhere(['resume_item.resume_id'=>$resume_id])
            ->andWhere('ciri.conciliation_item_id IS NULL');

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function getWorkflowCreateLog(){}

    /**
     * @inheritdoc
     * @return string
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
                self::STATE_CANCELED,
                self::STATE_CONCILED
            ],
            self::STATE_CANCELED => [
                self::STATE_CLOSED
            ]
        ];
    }

    /**
     * Retorno el total cargado en los items del resumen.
     *
     */
    public function getTotal()
    {
        $total = ['debit' => 0, 'credit'=> 0];

        foreach($this->getResumeItems()->all()  as $item) {
            $total['debit'] +=  ($item->debit > 0 ?  $item->debit : 0 );
            $total['credit'] +=  ($item->credit > 0 ?  $item->credit : 0 );
        }
        return $total;
    }
}