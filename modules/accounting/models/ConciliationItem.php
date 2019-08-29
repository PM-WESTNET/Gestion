<?php

namespace app\modules\accounting\models;

use app\modules\accounting\components\CountableMovement;
use Yii;

/**
 * This is the model class for table "conciliation_item".
 *
 * @property integer $conciliation_item_id
 * @property integer $conciliation_id
 * @property double $amount
 * @property string $date
 * @property string $description
 *
 *
 * @property Conciliation $conciliation
 * @property ConciliationItemHasAccountMovementItem[] $conciliationItemHasAccountMovementItems
 * @property ConciliationItemHasResumeItem[] $conciliationItemHasResumeItems
 */
class ConciliationItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conciliation_item';
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
            [['conciliation_id'], 'required'],
            [['conciliation_id'], 'integer'],
            [['amount'], 'number'],
            [['date', 'conciliation'], 'safe'],
            [['date'], 'date'],
            [['description'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'conciliation_item_id' => Yii::t('accounting', 'Conciliation Item ID'),
            'conciliation_id' => Yii::t('accounting', 'Conciliation ID'),
            'amount' => Yii::t('accounting', 'Amount'),
            'date' => Yii::t('accounting', 'Date'),
            'description' => Yii::t('accounting', 'Description'),
            'conciliation' => Yii::t('accounting', 'Conciliation'),
            'conciliationItemHasAccountMovementItems' => Yii::t('accounting', 'ConciliationItemHasAccountMovementItems'),
            'conciliationItemHasResumeItems' => Yii::t('accounting', 'ConciliationItemHasResumeItems'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliation()
    {
        return $this->hasOne(Conciliation::className(), ['conciliation_id' => 'conciliation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliationItemHasAccountMovementItems()
    {
        return $this->hasMany(ConciliationItemHasAccountMovementItem::className(), ['conciliation_item_id' => 'conciliation_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliationItemHasResumeItems()
    {
        return $this->hasMany(ConciliationItemHasResumeItem::className(), ['conciliation_item_id' => 'conciliation_item_id']);
    }

    public function getResumeItems()
    {
        return $this->hasMany(ResumeItem::class, ['resume_item_id' => 'resume_item_id'])->viaTable('conciliation_item_has_resume_item', ['conciliation_item_id' => 'conciliation_item_id']);
    }

    public function getAccountMovementItems()
    {
        return $this->hasMany(AccountMovementItem::class, ['account_movement_id' => 'account_movement_id'])->viaTable('conciliation_item_has_account_movement_item', ['conciliation_item_id' => 'conciliation_item_id']);
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
            $this->date = Yii::$app->formatter->asDate($this->date, 'dd-MM-yyyy');
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
     * Strong relations: ConciliationItemHasAccountMovementItems, ConciliationItemHasResumeItems.
     */
    public function getDeletable()
    {
        return ($this->conciliation->status=='draft');
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Conciliation.
     */
    protected function unlinkWeakRelations()
    {
        $this->unlinkAll('conciliationItemHasResumeItems', true);
        $this->unlinkAll('conciliationItemHasAccountMovementItems', true);
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

    public function addResumeItem($resume_item, $createMov = false)
    {
        $has = new ConciliationItemHasResumeItem();
        $has->resume_item_id = $resume_item->resume_item_id;
        $this->link('conciliationItemHasResumeItems', $has);

        if($createMov) {
            $description = $resume_item->operationType->name;
            $countableMovement= CountableMovement::getInstance();
            $mvItem1 = new AccountMovementItem();
            $mvItem1->account_id = $this->conciliation->moneyBoxAccount->account_id;
            if ($resume_item->debit > 0) {
                $mvItem1->debit = $resume_item->debit;
                $mvItem1->credit =0 ;
            }else {
                $mvItem1->debit = 0;
                $mvItem1->credit = $resume_item->credit;
            }

            $mvItem1->save();

            $mvItem2 = new AccountMovementItem();
            $mvItem2->account_id = $resume_item->moneyBoxHasOperationType->account_id;
            if ($resume_item->debit > 0) {
                $mvItem2->debit = 0;
                $mvItem2->credit = $resume_item->debit;
            }else {
                $mvItem2->debit = $resume_item->credit;
                $mvItem2->credit = 0;
            }

            $mvItem2->save();

            $movement_id = $countableMovement->createMovement($description,$this->conciliation->moneyBoxAccount->company_id,
                [$mvItem1, $mvItem2], null, null, $resume_item->date);

            if ($movement_id) {
                $this->addAccountItem($mvItem1);
            }

        }
    }

    public function addAccountItem($account_item)
    {
        $has = new ConciliationItemHasAccountMovementItem();
        $has->account_movement_item_id = $account_item;
        $this->link('conciliationItemHasAccountMovementItems', $has);
    }

    public function getTotals()
    {
        $debit = 0;
        $credit = 0;

        foreach ($this->resumeItems as $item) {
            if ($item->debit > 0) {
                $debit += $item->debit;
            }else {
                $credit += $item->credit;
            }
        }

//        foreach ($this->accountMovementItems as $item) {
//            if ($item->debit !== null) {
//                $debit += $item->debit;
//            }else {
//                $credit += $item->credit;
//            }
//        }

        return [
          'debit' => $debit,
          'credit' => $credit,
          'total' => (-$debit + $credit)
        ];
    }
}
