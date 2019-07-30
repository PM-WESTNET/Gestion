<?php

namespace app\modules\partner\models;

use Yii;

/**
 * This is the model class for table "partner_liquidation_movement".
 *
 * @property int $partner_liquidation_movement_id
 * @property int $partner_liquidation_id
 * @property string $class
 * @property int $model_id
 * @property string $type
 *
 * @property PartnerLiquidation $partnerLiquidation
 */
class PartnerLiquidationMovement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_liquidation_movement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_liquidation_id', 'class', 'model_id', 'type'], 'required'],
            [['partner_liquidation_id', 'model_id'], 'integer'],
            [['class', 'type'], 'string', 'max' => 255],
            [['partner_liquidation_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerLiquidation::className(), 'targetAttribute' => ['partner_liquidation_id' => 'partner_liquidation_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'partner_liquidation_movement_id' => Yii::t('app', 'Partner Liquidation Movement ID'),
            'partner_liquidation_id' => Yii::t('app', 'Partner Liquidation ID'),
            'class' => Yii::t('app', 'Class'),
            'model_id' => Yii::t('app', 'Model ID'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerLiquidation()
    {
        return $this->hasOne(PartnerLiquidation::className(), ['partner_liquidation_id' => 'partner_liquidation_id']);
    }
}
