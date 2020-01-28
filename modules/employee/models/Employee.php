<?php

namespace app\modules\employee\models;

use app\components\helpers\CuitValidator;
use app\modules\accounting\models\Account;
use app\modules\sale\models\TaxCondition;
use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property integer $employee_id
 * @property string $name
 * @property string $business_name
 * @property string $tax_identification
 * @property string $address
 * @property string $bill_type
 * @property string $phone
 * @property string $phone2
 * @property string $description
 *
 * @property TaxCondition $taxCondition
 * @property Account $account
 * @property ProviderBill[] $employeeBills
 * @property ProviderPayment[] $employeePayments
 */
class Employee extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','bill_type', 'tax_condition_id', 'tax_identification'], 'required'],
            [['account_id'], 'safe'],
            [['description'], 'string'],
            [['name', 'business_name', 'address'], 'string', 'max' => 255],
            [['tax_identification', 'phone', 'phone2'], 'string', 'max' => 45],
            [['bill_type'], 'in', 'range' => ['A','B','C']],
            [['account_id'], 'number'],
            ['tax_identification', CuitValidator::className()]
        ];
        if (Yii::$app->getModule('accounting')) {
            $rules[] = [['account_id'], 'number'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_id' => Yii::t('app', 'Employee'),
            'name' => Yii::t('app', 'Name'),
            'business_name' => Yii::t('app', 'Business Name'),
            'tax_identification' => Yii::t('app', 'Tax Identification'),
            'address' => Yii::t('app', 'Address'),
            'bill_type' => Yii::t('app', 'Bill Type'),
            'phone' => Yii::t('app', 'Phone'),
            'phone2' => Yii::t('app', 'Phone 2'),
            'description' => Yii::t('app', 'Description'),
            'account_id' => Yii::t('accounting', 'Account'),
            'tax_condition_id' => Yii::t('app', 'Tax Condition'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBills()
    {
        return $this->hasMany(EmployeeBill::className(), ['employee_id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeePayments()
    {
        return $this->hasMany(EmployeePayment::className(), ['employee_id' => 'employee_id']);
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
    public function getTaxCondition()
    {
        return $this->hasOne(TaxCondition::className(), ['tax_condition_id' => 'tax_condition_id']);
    }

    public function getDeletable(){
    
        if($this->getEmployeeBills()->exists()){
            return false;
        }
        if($this->getEmployeePayments()->exists()){
            return false;
        }
        return true;
    }

    public function getFullname() {
        return $this->name . ' (' .$this->tax_identification . ')';
    }

    /**
     * @return array
     * Devuelve todos los posibles tipos de comprobantes.
     */
    public static function getAllBillTypes()
    {
        return [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C'
        ];
    }
}
