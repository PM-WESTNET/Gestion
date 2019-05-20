<?php

namespace app\modules\cobrodigital\models;

use app\modules\log\db\ActiveRecord;
use Yii;
use app\modules\cobrodigital\models\PaymentCardFile;

/**
 * This is the model class for table "payment_card".
 *
 * @property int $payment_card_id
 * @property int $payment_card_file_id
 * @property string $code_19_digits
 * @property string $code_29_digits
 * @property string $url
 * @property int $used
 *
 * @property PaymentCardFile $paymentCardFile
 */
class PaymentCard extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_card_file_id', 'code_19_digits', 'code_29_digits', 'url'], 'required'],
            [['payment_card_file_id', 'used'], 'integer'],
            [['url'], 'string'],
            [['code_19_digits', 'code_29_digits'], 'string', 'max' => 255],
            [['payment_card_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentCardFile::class, 'targetAttribute' => ['payment_card_file_id' => 'payment_card_file_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_card_id' => Yii::t('cobrodigital', 'Payment Card ID'),
            'payment_card_file_id' => Yii::t('cobrodigital', 'Payment Card File ID'),
            'code_19_digits' => Yii::t('cobrodigital', 'Code 19 Digits'),
            'code_29_digits' => Yii::t('cobrodigital', 'Code 29 Digits'),
            'url' => Yii::t('cobrodigital', 'Url'),
            'used' => Yii::t('cobrodigital', 'Used'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentCardFile()
    {
        return $this->hasOne(PaymentCardFile::class, ['payment_card_file_id' => 'payment_card_file_id']);
    }

    public static function getUnusedPaymentCardsQty() {
        return PaymentCard::find()->where(['used' => 0])->count();
    }
}
