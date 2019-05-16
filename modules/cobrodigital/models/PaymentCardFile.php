<?php

namespace app\modules\cobrodigital\models;

use app\modules\cobrodigital\components\PaymentCardReader;
use app\modules\log\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "payment_card_file".
 *
 * @property int $payment_card_file_id
 * @property string $upload_date
 * @property string $file_name
 * @property string $path
 *
 * @property PaymentCard[] $paymentCards
 */
class PaymentCardFile extends ActiveRecord
{
    const STATUS_DRAFT = 'draft';
    const STATUS_IMPORTED = 'imported';

    public static function tableName()
    {
        return 'payment_card_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path'], 'string'],
            [['status'], 'safe'],
            [['upload_date', 'file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_card_file_id' => Yii::t('cobrodigital', 'Payment Card File ID'),
            'upload_date' => Yii::t('cobrodigital', 'Upload Date'),
            'file_name' => Yii::t('cobrodigital', 'File Name'),
            'path' => Yii::t('cobrodigital', 'Path'),
            'status' => Yii::t('cobrodigital', 'Status')
        ];
    }

    public function getDeletable()
    {
        if($this->getPaymentCards()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentCards()
    {
        return $this->hasMany(PaymentCard::class, ['payment_card_file_id' => 'payment_card_file_id']);
    }

    public function import() {
        $data = PaymentCardReader::parse($this);
        $this->createPaymentsCards($data);
        $this->updateAttributes(['status' => PaymentCardFile::STATUS_IMPORTED]);
        return true;
    }

    private function createPaymentsCards($data) {
        $array_payment_card = [];

        foreach ($data as $payment_card_data) {
            array_push($array_payment_card, [$this->payment_card_file_id, $payment_card_data['code_19_digits'], $payment_card_data['code_29_digits'], $payment_card_data['url'], 'used' => 0]);
        }

        Yii::$app->db->createCommand()->batchInsert('payment_card', ['payment_card_file_id', 'code_19_digits', 'code_29_digits', 'url', 'used'], $array_payment_card)->execute();
    }
}
