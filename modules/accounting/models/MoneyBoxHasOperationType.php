<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "money_box_has_operation_type".
 *
 * @property integer $money_box_has_operation_type_id
 * @property integer $operation_type_id
 * @property integer $money_box_id
 * @property integer $account_id
 * @property integer $money_box_account_id
 * @property string $code
 *
 * @property MoneyBox $moneyBox
 * @property MoneyBoxAccount $moneyBoxAccount
 * @property Account $account
 * @property OperationType $operationType
 */
class MoneyBoxHasOperationType extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_box_has_operation_type';
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
            [['money_box_id', 'account_id'], 'required'],
            [['operation_type_id', 'money_box_id', 'account_id', 'money_box_account_id'], 'integer'],
            [['code'], 'string'],
            [['moneyBox', 'operationType', 'account', 'moneyBoxAccount','operation_type_id', 'code'], 'safe'],
            /**[['operation_type_id', 'money_box_id', 'account_id'], 'unique',
                'targetAttribute' => ['operation_type_id', 'money_box_id', 'account_id'],
                'message' => Yii::t('accounting', 'The combination of Operation Type, Money Box and Account has already been used.')
            ],**/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'operation_type_id' => Yii::t('accounting', 'Operation Type'),
            'money_box_id' => Yii::t('accounting', 'Money Box ID'),
            'money_box_account_id' => Yii::t('accounting', 'Money Box Account'),
            'moneyBox' => Yii::t('accounting', 'Money Box'),
            'moneyBoxAccount' => Yii::t('accounting', 'Money Box Account'),
            'operationType' => Yii::t('accounting', 'OperationType'),
            'account_id' => Yii::t('accounting', 'Account'),
            'code' => Yii::t('accounting', 'Código'),
        ];
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
    public function getOperationType()
    {
        return $this->hasOne(OperationType::className(), ['operation_type_id' => 'operation_type_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['account_id' => 'account_id']);
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
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MoneyBox, OperationType.
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
