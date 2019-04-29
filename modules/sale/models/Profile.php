<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property integer $profile_id
 * @property string $name
 * @property string $value
 * @property integer $create_timestamp
 * @property integer $update_timestamp
 * @property integer $customer_id
 * @property integer $profile_class_id
 *
 * @property Customer $customer
 * @property ProfileClass $profileClass
 */
class Profile extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['create_timestamp', 'update_timestamp', 'customer_id', 'profile_class_id'], 'integer'],
            [['customer_id', 'profile_class_id'], 'required'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profile_id' => Yii::t('app', 'Profile ID'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
            'create_timestamp' => Yii::t('app', 'Create Timestamp'),
            'update_timestamp' => Yii::t('app', 'Update Timestamp'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'profile_class_id' => Yii::t('app', 'Profile Class ID'),
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
    public function getProfileClass()
    {
        return $this->hasOne(ProfileClass::className(), ['profile_class_id' => 'profile_class_id']);
    }
    
    public function getDeletable(){
    
        return true;
        
    }
}
