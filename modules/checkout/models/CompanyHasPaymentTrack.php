<?php

namespace app\modules\checkout\models;

use Yii;
use app\modules\log\db\ActiveRecord;

/**
 * This is the model class for table "company_has_payment_track".
 *
 * @property int $company_has_payment_track_id
 * @property int $company_id
 * @property int $payment_method_id
 * @property int $track_id
 * @property string $status
 *
 * @property Company $company
 * @property PaymentMethod $paymentMethod
 * @property Track $track
 */
class CompanyHasPaymentTrack extends ActiveRecord
{

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    public static function tableName()
    {
        return 'company_has_payment_track';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'payment_method_id', 'track_id'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'company_id']],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['payment_method_id' => 'payment_method_id']],
            [['track_id'], 'exist', 'skipOnError' => true, 'targetClass' => Track::class, 'targetAttribute' => ['track_id' => 'track_id']],
            [['status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'company_has_payment_track_id' => Yii::t('app', 'Company Has Payment Track ID'),
            'company_id' => Yii::t('app', 'Company ID'),
            'payment_method_id' => Yii::t('app', 'Payment Method ID'),
            'track_id' => Yii::t('app', 'Track ID'),
            'status' => Yii::t('app', 'Status')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, ['payment_method_id' => 'payment_method_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrack()
    {
        return $this->hasOne(Track::class, ['track_id' => 'track_id']);
    }
}
