<?php

namespace app\modules\checkout\models;

use Yii;
use app\modules\log\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "track".
 *
 * @property int $track_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property boolean $use_payment_card
 *
 * @property CompanyHasPaymentTrack[] $companyHasPaymentTracks
 */
class Track extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'track';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['description'], 'string'],
            [['use_payment_card'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'slug'=>[
                'class' => SluggableBehavior::class,
                'slugAttribute' => 'slug',
                'attribute' => 'name',
                'ensureUnique' => true,
                'immutable' => true
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'track_id' => Yii::t('app', 'Track ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'use_payment_card' => Yii::t('app', 'Use a payment_card?')
        ];
    }

    public function getDeletable()
    {
        if($this->getCompanyHasPaymentTracks()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyHasPaymentTracks()
    {
        return $this->hasMany(CompanyHasPaymentTrack::class, ['track_id' => 'track_id']);
    }
}
