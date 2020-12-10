<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "ip_address".
 *
 * @property int $ip_address_id
 * @property int $ip_address
 * @property string $status
 * @property int $ip_range_id
 *
 * @property IpRange $ipRange
 */
class IpAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ip_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip_address', 'status', 'ip_range_id'], 'required'],
            [['ip_address', 'ip_range_id'], 'integer'],
            [['status'], 'string'],
            [['ip_range_id'], 'exist', 'skipOnError' => true, 'targetClass' => IpRange::className(), 'targetAttribute' => ['ip_range_id' => 'ip_range_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ip_address_id' => Yii::t('app', 'Ip Address ID'),
            'ip_address' => Yii::t('app', 'Ip Address'),
            'status' => Yii::t('app', 'Status'),
            'ip_range_id' => Yii::t('app', 'Ip Range ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIpRange()
    {
        return $this->hasOne(IpRange::className(), ['ip_range_id' => 'ip_range_id']);
    }
}
