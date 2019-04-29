<?php

namespace app\modules\provider\models;

use app\modules\accounting\models\Account;
use Yii;

/**
 * This is the model class for table "provider_bill_item".
 *
 * @property integer $provider_bill_item_id
 * @property integer $provider_bill_id
 * @property integer $account_id
 * @property double $amount
 *
 * @property Account $account
 * @property ProviderBill $providerBill
 */
class ProviderBillItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_bill_item';
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
            [['provider_bill_id'], 'required'],
            [['provider_bill_id', 'account_id'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string'],
            [['account', 'providerBill'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_bill_item_id' => Yii::t('app', 'Provider Bill Item ID'),
            'provider_bill_id' => Yii::t('app', 'Provider Bill ID'),
            'account_id' => Yii::t('app', 'Account') . ' ' .Yii::t('app', '(optional)'),
            'amount' => Yii::t('app', 'Amount'),
            'account' => Yii::t('app', 'Account'),
            'providerBill' => Yii::t('app', 'ProviderBill'),
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
    public function getProviderBill()
    {
        return $this->hasOne(ProviderBill::className(), ['provider_bill_id' => 'provider_bill_id']);
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
     * Weak relations: Account, ProviderBill.
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
