<?php

namespace app\modules\checkout\models;

use Yii;

/**
 * This is the model class for table "pago_facil_payment".
 *
 * @property integer $pago_facil_payment_id
 * @property integer $pago_facil_transmition_file_pago_facil_transmition_file_id
 * @property integer $payment_payment_id
 *
 * @property PagoFacilTransmitionFile $pagoFacilTransmitionFilePagoFacilTransmitionFile
 * @property Payment $paymentPayment
 */
class PagoFacilPayment extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pago_facil_payment';
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
            [['pago_facil_transmition_file_pago_facil_transmition_file_id', 'payment_payment_id'], 'required'],
            [['pago_facil_transmition_file_pago_facil_transmition_file_id', 'payment_payment_id'], 'integer'],
            [['pagoFacilTransmitionFilePagoFacilTransmitionFile', 'paymentPayment'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pago_facil_payment_id' => 'Pago Facil Payment ID',
            'payment_id' => 'Payment ID',
            'pago_facil_transmition_file_pago_facil_transmition_file_id' => 'Pago Facil Transmition File Pago Facil Transmition File ID',
            'payment_payment_id' => 'Payment Payment ID',
            'pagoFacilTransmitionFilePagoFacilTransmitionFile' => 'PagoFacilTransmitionFilePagoFacilTransmitionFile',
            'paymentPayment' => 'PaymentPayment',
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagoFacilTransmitionFilePagoFacilTransmitionFile()
    {
        return $this->hasOne(PagoFacilTransmitionFile::className(), ['pago_facil_transmition_file_id' => 'pago_facil_transmition_file_pago_facil_transmition_file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentPayment()
    {
        return $this->hasOne(Payment::class, ['payment_id' => 'payment_payment_id']);
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
     * Weak relations: PagoFacilTransmitionFilePagoFacilTransmitionFile, PaymentPayment.
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
