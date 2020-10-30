<?php

namespace app\modules\firstdata\models;

use Yii;

/**
 * This is the model class for table "firstdata_import_payment".
 *
 * @property int $firstdata_import_payment_id
 * @property int $firstdata_import_id
 * @property int $customer_code
 * @property int $customer_id
 * @property int $payment_id
 * @property string $status
 *
 * @property Customer $customer
 * @property FirstdataImport $firstdataImport
 * @property Payment $payment
 */
class FirstdataImportPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_import_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_import_id', 'customer_code', 'status'], 'required'],
            [['firstdata_import_payment_id', 'firstdata_import_id', 'customer_code', 'customer_id', 'payment_id'], 'integer'],
            [['status', 'error'], 'string'],
            [['amount'], 'double'],
            [['firstdata_import_payment_id'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['firstdata_import_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataImport::className(), 'targetAttribute' => ['firstdata_import_id' => 'firstdata_import_id']],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::className(), 'targetAttribute' => ['payment_id' => 'payment_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_import_payment_id' => Yii::t('app', 'Firstdata Import Payment ID'),
            'firstdata_import_id' => Yii::t('app', 'Firstdata Import ID'),
            'customer_code' => Yii::t('app', 'Customer Code'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'status' => Yii::t('app', 'Status'),
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
    public function getFirstdataImport()
    {
        return $this->hasOne(FirstdataImport::className(), ['firstdata_import_id' => 'firstdata_import_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }
}
