<?php

namespace app\modules\employee\models;

use app\modules\accounting\models\Account;
use Yii;

/**
 * This is the model class for table "employee_bill_item".
 *
 * @property integer $employee_bill_item_id
 * @property integer $employee_bill_id
 * @property integer $account_id
 * @property double $amount
 *
 * @property Account $account
 * @property EmployeeBill $employeeBill
 */
class EmployeeBillItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_bill_item';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'modifier' => [
                'class'=> 'app\components\db\ModifierBehavior'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employee_bill_id'], 'required'],
            [['employee_bill_id', 'account_id'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string'],
            [['account', 'employeeBill'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_bill_item_id' => Yii::t('app', 'Employee Bill Item ID'),
            'employee_bill_id' => Yii::t('app', 'Employee Bill ID'),
            'account_id' => Yii::t('app', 'Account') . ' ' .Yii::t('app', '(optional)'),
            'amount' => Yii::t('app', 'Amount'),
            'account' => Yii::t('app', 'Account'),
            'employeeBill' => Yii::t('app', 'EmployeeBill'),
            'description' => Yii::t('app', 'Description'),
        ];
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
    public function getEmployeeBill()
    {
        return $this->hasOne(EmployeeBill::className(), ['employee_bill_id' => 'employee_bill_id']);
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
     * Weak relations: Account, EmployeeBill.
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
