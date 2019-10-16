<?php

namespace app\modules\automaticdebit\models;

use Yii;
use app\modules\checkout\models\Payment;

/**
 * This is the model class for table "debit_direct_import_has_payment".
 *
 * @property int $debit_direct_import_has_payment
 * @property int $payment_id
 * @property int $debit_direct_import_id
 *
 * @property DebitDirectImport $debitDirectImport
 * @property Payment $payment
 */
class DebitDirectImportHasPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'debit_direct_import_has_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_id', 'debit_direct_import_id'], 'required'],
            [['payment_id', 'debit_direct_import_id'], 'integer'],
            [['debit_direct_import_id'], 'exist', 'skipOnError' => true, 'targetClass' => DebitDirectImport::class, 'targetAttribute' => ['debit_direct_import_id' => 'debit_direct_import_id']],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::class, 'targetAttribute' => ['payment_id' => 'payment_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'debit_direct_import_has_payment' => Yii::t('app', 'Debit Direct Import Has Payment'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'debit_direct_import_id' => Yii::t('app', 'Debit Direct Import ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebitDirectImport()
    {
        return $this->hasOne(DebitDirectImport::className(), ['debit_direct_import_id' => 'debit_direct_import_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['payment_id' => 'payment_id']);
    }
}
