<?php

namespace app\modules\afip\models;

use app\modules\provider\models\ProviderBill;
use app\modules\sale\models\Bill;
use Yii;

/**
 * This is the model class for table "taxes_book_item".
 *
 * @property integer $taxes_book_item_id
 * @property integer $page
 * @property integer $taxes_book_id
 * @property integer $bill_id
 * @property integer $provider_bill_id
 *
 * @property Bill $bill
 * @property ProviderBill $providerBill
 * @property TaxesBook $taxesBook
 */
class TaxesBookItem extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxes_book_item';
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
            [['page', 'taxes_book_id'], 'required'],
            [['page', 'taxes_book_id', 'bill_id', 'provider_bill_id'], 'integer'],
            [['bill', 'providerBill', 'taxesBook'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'taxes_book_item_id' => Yii::t('app', 'Taxes Book Item ID'),
            'page' => Yii::t('app', 'Page'),
            'taxes_book_id' => Yii::t('app', 'Taxes Book ID'),
            'bill_id' => Yii::t('app', 'Bill ID'),
            'provider_bill_id' => Yii::t('app', 'Provider Bill ID'),
            'bill' => Yii::t('app', 'Bill'),
            'providerBill' => Yii::t('app', 'ProviderBill'),
            'taxesBook' => Yii::t('app', 'TaxesBook'),
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
    public function getProviderBill()
    {
        return $this->hasOne(ProviderBill::className(), ['provider_bill_id' => 'provider_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxesBook()
    {
        return $this->hasOne(TaxesBook::className(), ['taxes_book_id' => 'taxes_book_id']);
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
     * Weak relations: Bill, ProviderBill, TaxesBook.
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
