<?php

namespace app\modules\sale\models;

use Yii;
use app\modules\sale\models\Customer;
use app\modules\sale\models\HourRange;

/**
 * This is the model class for table "customer_class_has_hour_range".
 *
 * @property integer $customer_has_hour_range
 * @property integer $customer_id
 * @property integer $hour_range_id
 *
 */
class CustomerHasHourRange extends \app\components\db\ActiveRecord
{

     private $oldClass;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_has_hour_range';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'hour_range_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => Yii::t('app', 'Customer'),
            'hour_range_id' => Yii::t('app', 'Hour range'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHourRange()
    {
        return $this->hasOne(HourRange::class, ['hour_range_id' => 'hour_range_id']);
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
