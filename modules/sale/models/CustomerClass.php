<?php

namespace app\modules\sale\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "customer_class".
 *
 * @property integer $customer_class_id
 * @property string $name
 * @property integer $code_ext
 * @property integer $is_invoiced
 * @property integer $tolerance_days
 * @property integer $colour
 * @property integer $percentage_bill
 * @property integer $days_duration
 * @property integer $service_enabled
 * @property integer $percentage_tolerance_debt
 * @property string  $status
 *
 * @property CustomerClassHasCustomer[] $customerClassHasCustomers
 * @property Customer[] $customers
 */
class CustomerClass extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_class';
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
            [['code_ext', 'is_invoiced', 'tolerance_days', 'percentage_bill', 'days_duration', 'service_enabled', 'percentage_tolerance_debt'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['colour'], 'string', 'max' => 54],
            [['percentage_bill'], 'integer','min' => 0, 'max' => 100],
            [['name','code_ext', 'is_invoiced', 'tolerance_days', 'percentage_bill', 'days_duration', 'colour', 'percentage_tolerance_debt', 'status'], 'required'],
            [['name', 'colour'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_class_id' => Yii::t('app', 'Customer Class ID'),
            'name' => Yii::t('app', 'Name'),
            'code_ext' => Yii::t('app', 'code_ext'),
            'is_invoiced' => Yii::t('app', 'Is Invoiced'),
            'tolerance_days' => Yii::t('app', 'Tolerance Days'),
            'colour' => Yii::t('app', 'Colour'),
            'percentage_bill' => Yii::t('app', 'Percentage Bill'),
            'days_duration' => Yii::t('app', 'Days Duration'),
            'service_enabled' => Yii::t('app', 'Service Enabled'),
            'percentage_tolerance_debt' => Yii::t('app', 'Tolerance in Debt'),
            'status' =>  Yii::t('app', 'Status'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerClassHasCustomers()
    {
        return $this->hasMany(CustomerClassHasCustomer::className(), ['customer_class_id' => 'customer_class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['customer_id' => 'customer_id'])->viaTable('customer_class_has_customer', ['customer_class_id' => 'customer_class_id']);
    }
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        /** @var Query $query */
        $cant = Customer::find()
            ->leftJoin('customer_class_has_customer cchc', 'customer.customer_id = cchc.customer_id' )
            ->where(['cchc.customer_class_id' => $this->customer_class_id])
            ->count();

        if($cant == 0){
            return true;
        }
        else{
            return false;
        }
        
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: CustomerClassHasCustomers, Customers.
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
    
    /**
     * TODO: implementar con attr default en modelo/db
     */
    public static function getDefault()
    {
        
        return self::find()->one();
        
    }

}
