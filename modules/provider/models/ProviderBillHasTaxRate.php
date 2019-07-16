<?php

namespace app\modules\provider\models;

use app\modules\sale\models\TaxRate;
use Yii;

/**
 * This is the model class for table "provider_bill_has_tax_rate".
 *
 * @property integer $provider_bill_id
 * @property integer $tax_rate_id
 * @property double $amount
 *
 * @property ProviderBill $providerBill
 * @property TaxRate $taxRate
 */
class ProviderBillHasTaxRate extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provider_bill_has_tax_rate';
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
            [['provider_bill_id', 'tax_rate_id', 'amount'], 'required'],
            [['provider_bill_id', 'tax_rate_id'], 'integer'],
            [['amount'], 'number'],
            [['providerBill', 'taxRate', 'net'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_bill_id' => Yii::t('app', 'Provider Bill'),
            'tax_rate_id' => Yii::t('app', 'Tax Rate'),
            'amount' => Yii::t('app', 'Amount'),
            'providerBill' => Yii::t('app', 'Provider Bill'),
            'taxRate' => Yii::t('app', 'Tax Rate'),
            'net' => Yii::t('app', 'Net')
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
     * Weak relations: ProviderBill, TaxRate.
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
