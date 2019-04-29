<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "conciliation_item_has_resume_item".
 *
 * @property integer $conciliation_item_id
 * @property integer $resume_item_id
 *
 * @property ConciliationItem $conciliationItem
 * @property ResumeItem $resumeItem
 */
class ConciliationItemHasResumeItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conciliation_item_has_resume_item';
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
            [['conciliation_item_id', 'resume_item_id'], 'required'],
            [['conciliation_item_id', 'resume_item_id'], 'integer'],
            [['conciliationItem', 'resumeItem'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'conciliation_item_id' => Yii::t('accounting', 'Conciliation Item ID'),
            'resume_item_id' => Yii::t('accounting', 'Resume Item ID'),
            'conciliationItem' => Yii::t('accounting', 'ConciliationItem'),
            'resumeItem' => Yii::t('accounting', 'ResumeItem'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliationItem()
    {
        return $this->hasOne(ConciliationItem::className(), ['conciliation_item_id' => 'conciliation_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResumeItem()
    {
        return $this->hasOne(ResumeItem::className(), ['resume_item_id' => 'resume_item_id']);
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
     * Weak relations: ConciliationItem, ResumeItem.
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

}
