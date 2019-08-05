<?php

namespace app\modules\automaticdebit\models;

use kcfinder\text;
use Yii;

/**
 * This is the model class for table "debit_direct_failed_payment".
 *
 * @property int $debit_direct_failed_payment_id
 * @property string $customer_code
 * @property double $amount
 * @property string $date
 * @property string $cbu
 * @property integer $import_id
 * @property text $error
 */
class DebitDirectFailedPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'debit_direct_failed_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'customer_code', 'date', 'cbu'], 'required'],
            [['amount'], 'number'],
            [['error'], 'string'],
            [['customer_code', 'date', 'cbu'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'debit_direct_failed_payment_id' => Yii::t('app', 'Debit Direct Failed Payment ID'),
            'customer_code' => Yii::t('app', 'Customer Code'),
            'amount' => Yii::t('app', 'Amount'),
            'date' => Yii::t('app', 'Date'),
            'cbu' => Yii::t('app', 'Cbu'),
            'import_id' => Yii::t('app', 'Import'),
            'error' => Yii::t('app', 'Error'),
        ];
    }
}
