<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "customer_class_has_customer".
 *
 * @property integer $customer_class_id
 * @property integer $customer_id
 * @property integer $date_updated
 *
 * @property Customer $customer
 * @property CustomerClass $customerClass
 */
class CustomerClassHasCustomer extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_class_has_customer';
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
            [['customer_class_id', 'customer_id', 'date_updated'], 'required'],
            [['customer_class_id', 'customer_id', 'date_updated'], 'integer'],
            [['customer', 'customerClass'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_class_id' => Yii::t('app', 'Customer Class ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'date_updated' => Yii::t('app', 'Date Updated'),
            'customer' => Yii::t('app', 'Customer'),
            'customerClass' => Yii::t('app', 'CustomerClass'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerClass()
    {
        return $this->hasOne(CustomerClass::className(), ['customer_class_id' => 'customer_class_id']);
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
     * Weak relations: Customer, CustomerClass.
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
