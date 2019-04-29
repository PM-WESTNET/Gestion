<?php

namespace app\modules\partner\models;

use Yii;

/**
 * This is the model class for table "partner_distribution_model_has_partner".
 *
 * @property integer $partner_distribution_model_has_partner_id
 * @property integer $partner_id
 * @property integer $partner_distribution_model_id
 * @property double $percentage
 *
 * @property Partner $partner
 * @property PartnerDistributionModel $partnerDistributionModel
 */
class PartnerDistributionModelHasPartner extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'partner_distribution_model_has_partner';
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
            [['partner_id', 'partner_distribution_model_id', 'percentage'], 'required'],
            [['partner_id', 'partner_distribution_model_id'], 'integer'],
            [['percentage'], 'number', 'min'=>0, 'max'=>100],
            [['partner_id', 'partner_distribution_model_id'], 'unique',
                'targetAttribute' => ['partner_id', 'partner_distribution_model_id'],
                'message' => Yii::t('partner', 'Can\'t add the same partner twice.')
            ],
            [['partner', 'partnerDistributionModel'], 'safe'],
            [['percentage'], 'validatePercentage']
        ];
    }

    /**
     * Valido que no se pase del 100%
     * @param $attribute
     * @param $params
     */
    public function validatePercentage($attribute,$params)
    {
        if($this->partnerDistributionModel->getTotalPercentage(($this->isNewRecord ? null : $this->partner_distribution_model_has_partner_id )) + $this->percentage > 100) {
            $this->addError($attribute, Yii::t('partner', 'The total percentage can\'t be greater than 100.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'partner_distribution_model_has_partner_id' => Yii::t('partner', 'Partner Distribution Model Has Partner ID'),
            'partner_id' => Yii::t('partner', 'Partner'),
            'partner_distribution_model_id' => Yii::t('partner', 'Partner Distribution Model'),
            'percentage' => Yii::t('app', 'Percentage'),
            'partner' => Yii::t('partner', 'Partner'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['partner_id' => 'partner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
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
     * Weak relations: Partner, PartnerDistributionModel.
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
