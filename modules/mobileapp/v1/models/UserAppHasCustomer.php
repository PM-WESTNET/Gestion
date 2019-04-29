<?php

namespace app\modules\mobileapp\v1\models;

use Yii;

/**
 * This is the model class for table "user_app_has_customer".
 *
 * @property integer $user_app_has_customer_id
 * @property integer $user_app_id
 * @property integer $customer_id
 * @property integer $customer_code
 *
 * @property Customer $customer
 * @property UserApp $userApp
 * @property ValidationCode[] $validationCodes
 */
class UserAppHasCustomer extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_app_has_customer';
    }
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_app_id'], 'required'],
            [['user_app_id', 'customer_id', 'customer_code'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_app_has_customer_id' => 'User App Has Customer ID',
            'user_app_id' => 'User App ID',
            'customer_id' => 'Customer ID',
            'customer_code' => 'Customer Code',
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
    public function getUserApp()
    {
        return $this->hasOne(UserApp::className(), ['user_app_id' => 'user_app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValidationCodes()
    {
        return $this->hasMany(ValidationCode::className(), ['user_app_has_customer_id' => 'user_app_has_customer_id']);
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
     * Weak relations: Customer, UserApp, ValidationCodes.
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
