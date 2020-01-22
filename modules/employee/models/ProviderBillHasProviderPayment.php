<?php

namespace app\modules\provider\models;

use Yii;

/**
 * This is the model class for table "provider_bill_has_provider_payment".
 *
 * @property integer $provider_bill_id
 * @property integer $provider_payment_id
 * @property double $amount
 *
 * @property ProviderBill $providerBill
 * @property ProviderPayment $providerPayment
 */
class ProviderBillHasProviderPayment extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_bill_has_provider_payment';
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
            [['provider_bill_id', 'provider_payment_id'], 'required'],
            [['provider_bill_id', 'provider_payment_id'], 'integer'],
            [['amount'], 'number'],
            [['providerBill', 'providerPayment'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_bill_id' => Yii::t('app', 'Provider Bill ID'),
            'provider_payment_id' => Yii::t('app', 'Provider Payment ID'),
            'amount' => Yii::t('app', 'Amount'),
            'providerBill' => Yii::t('app', 'ProviderBill'),
            'providerPayment' => Yii::t('app', 'ProviderPayment'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderBill()
    {
        return $this->hasOne(ProviderBill::className(), ['provider_bill_id' => 'provider_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderPayment()
    {
        return $this->hasOne(ProviderPayment::className(), ['provider_payment_id' => 'provider_payment_id']);
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
     * Weak relations: ProviderBill, ProviderPayment.
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
