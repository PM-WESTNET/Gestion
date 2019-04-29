<?php

namespace app\modules\checkout\models;

use app\modules\sale\models\Bill;
use Yii;

/**
 * This is the model class for table "bill_has_payment".
 *
 * @property integer $bill_has_payment_id
 * @property integer $bill_id
 * @property integer $payment_id
 * @property double $amount
 *
 * @property Bill $bill
 * @property Payment $payment
 */
class BillHasPayment extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bill_has_payment';
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
            [['bill_id', 'payment_id'], 'required'],
            [['bill_id', 'payment_id'], 'integer'],
            [['amount'], 'number'],
            [['bill', 'payment'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bill_has_payment_id' => Yii::t('app', 'Bill Has Payment ID'),
            'bill_id' => Yii::t('app', 'Bill ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'amount' => Yii::t('app', 'Amount'),
            'bill' => Yii::t('app', 'Bill'),
            'payment' => Yii::t('app', 'Payment'),
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
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }
    
        
        
        
                 
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->payment->status=="draft");
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Bill, Payment.
     */
    protected function unlinkWeakRelations()
    {
        $borrar = true;
        foreach($this->payment->billHasPayments as $bhp) {
            if ($bhp->bill_has_payment_id != $bhp->bill_has_payment_id) {
                $borrar = false;
            }
        }
        if ($borrar) {
            $this->unlinkAll("payment", true);
        }
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
