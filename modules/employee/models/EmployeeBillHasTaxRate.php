<?php

namespace app\modules\employee\models;

use app\modules\sale\models\TaxRate;
use Yii;

/**
 * This is the model class for table "employee_bill_has_tax_rate".
 *
 * @property integer $employee_bill_id
 * @property integer $tax_rate_id
 * @property double $amount
 *
 * @property EmployeeBill $employeeBill
 * @property TaxRate $taxRate
 */
class EmployeeBillHasTaxRate extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_bill_has_tax_rate';
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
            [['employee_bill_id', 'tax_rate_id', 'amount'], 'required'],
            [['employee_bill_id', 'tax_rate_id'], 'integer'],
            [['amount'], 'number'],
            [['employeeBill', 'taxRate', 'net'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_bill_id' => Yii::t('app', 'Employee Bill'),
            'tax_rate_id' => Yii::t('app', 'Tax Rate'),
            'amount' => Yii::t('app', 'Amount'),
            'employeeBill' => Yii::t('app', 'Employee Bill'),
            'taxRate' => Yii::t('app', 'Tax Rate'),
            'net' => Yii::t('app', 'Net')
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
    public function getTaxRate()
    {
        return $this->hasOne(TaxRate::className(), ['tax_rate_id' => 'tax_rate_id']);
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
     * Weak relations: EmployeeBill, TaxRate.
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
