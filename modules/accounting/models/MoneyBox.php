<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "money_box".
 *
 * @property integer $money_box_id
 * @property string $name
 * @property integer $money_box_type_id
 * @property integer $account_id
 *
 * @property Account $account
 * @property MoneyBoxType $moneyBoxType
 * @property MoneyBoxAccount[] $moneyBoxAccounts
 * @property MoneyBoxHasOperationType[] $moneyBoxHasOperationTypes
 * @property OperationType[] $operationTypes
 */
class MoneyBox extends \app\components\db\ActiveRecord
{
    private $_operationTypes;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_box';
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
            [['name', 'money_box_type_id'], 'required'],
            [['money_box_type_id', 'account_id'], 'integer'],
            [['account', 'moneyBoxType', 'operationTypes'], 'safe'],
            [['name'], 'string', 'max' => 150]
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money_box_id' => Yii::t('accounting', 'Money Box ID'),
            'name' => Yii::t('accounting', 'Name'),
            'money_box_type_id' => Yii::t('accounting', 'Money Box Type'),
            'moneyBoxType' => Yii::t('accounting', 'Money Box Type'),
            'moneyBoxAccounts' => Yii::t('accounting', 'Money Box Accounts'),
            'account_id' => Yii::t('accounting', 'Account'),
            'account' => Yii::t('accounting', 'Account'),

        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperationTypes()
    {
        return $this->hasMany(OperationType::className(), ['operation_type_id' => 'operation_type_id'])->viaTable('money_box_has_operation_type', ['money_box_id' => 'money_box_id']);
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
    public function getMoneyBoxType()
    {
        return $this->hasOne(MoneyBoxType::className(), ['money_box_type_id' => 'money_box_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccounts()
    {
        return $this->hasMany(MoneyBoxAccount::className(), ['money_box_id' => 'money_box_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxHasOperationTypes()
    {
        return $this->hasMany(MoneyBoxHasOperationType::className(), ['money_box_id' => 'money_box_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return !($this->getMoneyBoxAccounts()->count()>0);
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MoneyBoxType, MoneyBoxAccounts.
     */
    protected function unlinkWeakRelations()
    {
        $this->unlinkAll('moneyBoxHasOperationTypes', true);
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
     * @param $type
     * @return \yii\db\ActiveQuery
     * Busca los money box de acuerdo a su tipo
     */
    public static function findByMoneyBoxType($type_id)
    {
        return MoneyBox::find()->where(['money_box_type_id' => $type_id]);
    }
}
