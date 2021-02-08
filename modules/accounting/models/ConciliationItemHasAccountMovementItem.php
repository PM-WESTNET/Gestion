<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "conciliation_item_has_account_movement_item".
 *
 * @property integer $account_movement_item_id
 * @property integer $conciliation_item_id
 *
 * @property AccountMovementItem $accountMovementItem
 * @property ConciliationItem $conciliationItem
 */
class ConciliationItemHasAccountMovementItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conciliation_item_has_account_movement_item';
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
            [['account_movement_item_id', 'conciliation_item_id'], 'required'],
            [['account_movement_item_id', 'conciliation_item_id'], 'integer'],
            [['accountMovementItem', 'conciliationItem'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_movement_item_id' => Yii::t('accounting', 'Account Movement Item ID'),
            'conciliation_item_id' => Yii::t('accounting', 'Conciliation Item ID'),
            'accountMovementItem' => Yii::t('accounting', 'AccountMovementItem'),
            'conciliationItem' => Yii::t('accounting', 'ConciliationItem'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountMovementItem()
    {
        return $this->hasOne(AccountMovementItem::className(), ['account_movement_item_id' => 'account_movement_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConciliationItem()
    {
        return $this->hasOne(ConciliationItem::className(), ['conciliation_item_id' => 'conciliation_item_id']);
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
     * Weak relations: AccountMovementItem, ConciliationItem.
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
