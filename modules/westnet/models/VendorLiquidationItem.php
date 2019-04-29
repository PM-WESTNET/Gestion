<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\sale\models\Bill;

/**
 * This is the model class for table "vendor_liquidation_item".
 *
 * @property integer $vendor_liquidation_item_id
 * @property integer $vendor_liquidation_id
 * @property integer $bill_id
 * @property double $amount
 * @property integer $contract_detail_id
 * @property string $description
 *
 * @property Bill $bill
 * @property ContractDetail $contractDetail
 * @property VendorLiquidation $vendorLiquidation
 */
class VendorLiquidationItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vendor_liquidation_item';
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
            [['vendor_liquidation_id'], 'required'],
            [['vendor_liquidation_id', 'bill_id', 'contract_detail_id'], 'integer'],
            [['amount'], 'number'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vendor_liquidation_item_id' => Yii::t('westnet', 'Vendor Liquidation Item ID'),
            'vendor_liquidation_id' => Yii::t('westnet', 'Vendor Liquidation ID'),
            'bill_id' => Yii::t('westnet', 'Bill ID'),
            'amount' => Yii::t('app', 'Amount'),
            'contract_detail_id' => Yii::t('app', 'Customer').' | '.Yii::t('app', 'Contract'),
            'description' => Yii::t('app', 'Description'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bill::className(), ['bill_id' => 'bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractDetail()
    {
        return $this->hasOne(\app\modules\sale\modules\contract\models\ContractDetail::className(), ['contract_detail_id' => 'contract_detail_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendorLiquidation()
    {
        return $this->hasOne(VendorLiquidation::className(), ['vendor_liquidation_id' => 'vendor_liquidation_id']);
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
     * Weak relations: Bill, ContractDetail, VendorLiquidation.
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
     * Anula el importe del item, pero se mantiene para que no vuelva a ser liquidado
     */
    public function cancel()
    {
        
        $this->updateAttributes(['amount' => '0.0', 'description' => $this->description.' | '.Yii::t('app', 'Item Cancelled')]);
        
    }

}
