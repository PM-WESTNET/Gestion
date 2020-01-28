<?php

namespace app\modules\employee\models;

use Yii;

/**
 * This is the model class for table "employee_bill_has_employee_payment".
 *
 * @property integer $employee_bill_id
 * @property integer $employee_payment_id
 * @property double $amount
 *
 * @property EmployeeBill $employeeBill
 * @property EmployeePayment $employeePayment
 */
class EmployeeBillHasEmployeePayment extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_bill_has_employee_payment';
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
            [['employee_bill_id', 'employee_payment_id'], 'required'],
            [['employee_bill_id', 'employee_payment_id'], 'integer'],
            [['amount'], 'number'],
            [['employeeBill', 'employeePayment'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_bill_id' => Yii::t('app', 'Employee Bill ID'),
            'employee_payment_id' => Yii::t('app', 'Employee Payment ID'),
            'amount' => Yii::t('app', 'Amount'),
            'employeeBill' => Yii::t('app', 'EmployeeBill'),
            'employeePayment' => Yii::t('app', 'EmployeePayment'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBill()
    {
        return $this->hasOne(EmployeeBill::className(), ['employee_bill_id' => 'employee_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeePayment()
    {
        return $this->hasOne(EmployeePayment::className(), ['employee_payment_id' => 'employee_payment_id']);
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
     * Weak relations: EmployeeBill, EmployeePayment.
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
