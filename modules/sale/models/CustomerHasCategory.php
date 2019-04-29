<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "customer_category_has_customer".
 *
 * @property integer $customer_category_id
 * @property integer $customer_id
 * @property integer $date_updated
 *
 * @property Customer $customer
 * @property CustomerCategory $customerCategory
 */
class CustomerHasCategory extends \app\components\db\ActiveRecord
{

    private $oldCategory;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_category_has_customer';
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
            [['customer_category_id', 'customer_id', 'date_updated'], 'required'],
            [['customer_category_id', 'customer_id', 'date_updated'], 'integer'],
            [['customer', 'customerCategory'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_category_id' => Yii::t('app', 'Customer Category ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'date_updated' => Yii::t('app', 'Date Updated'),
            'customer' => Yii::t('app', 'Customer'),
            'customerCategory' => Yii::t('app', 'CustomerCategory'),
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
    public function getCustomerCategory()
    {
        return $this->hasOne(CustomerCategory::className(), ['customer_category_id' => 'customer_category_id']);
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
     * Weak relations: Customer, CustomerCategory.
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
    
    public function beforeSave($insert) 
    {
        if ($insert) {
            $this->oldCategory = $this->customer->customerCategories;
        }
        
        parent::beforeSave($insert);
        return true;
    }
    
    
    public function afterSave($insert, $changedAttributes) {
        if ($insert && $this->oldCategory && ($this->customer_category_id !== $this->oldCategory->customer_category_id)) {
            $log= new CustomerLog();
            $log->createUpdateLog($this->customer_id, $this->attributeLabels()['customerCategory'], $this->oldCategory->name, $this->customerCategory->name, 'Customer', $this->customerCategory->customer_category_id);
        }
        
        parent::afterSave($insert, $changedAttributes);
        return true;
    }

}
