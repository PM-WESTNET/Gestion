<?php

namespace app\modules\partner\models;

use Yii;
use app\modules\partner\models\PartnerMovement;

/**
 * This is the model class for table "partner_liquidation".
 *
 * @property integer $partner_liquidation_id
 * @property string $date
 * @property integer $partner_distribution_model_has_partner_id
 * @property double $credit
 * @property double $debit
 *
 * @property PartnerDistributionModelHasPartner $partnerDistributionModelHasPartner
 */
class PartnerLiquidation extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'partner_liquidation';
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
            [['date', 'partnerDistributionModelHasPartner'], 'safe'],
            [['date'], 'date'],
            [['partner_distribution_model_has_partner_id'], 'integer'],
            [['debit', 'credit'], 'double'],
            [['partner_distribution_model_has_partner_id'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'partner_liquidation_id' => Yii::t('partner', 'Partner Liquidation ID'),
            'date' => Yii::t('partner', 'Date'),
            'partner_distribution_model_has_partner_id' => Yii::t('partner', 'Partner Distribution Model ID'),
            'partnerDistributionModelHasPartner' => Yii::t('partner', 'PartnerDistributionModel'),
            'debit' => Yii::t('app', 'Debit'),
            'credit' => Yii::t('app', 'Credit'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModelHasPartner()
    {
        return $this->hasOne(PartnerDistributionModelHasPartner::class, ['partner_distribution_model_has_partner_id' => 'partner_distribution_model_has_partner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerLiquidationMovements()
    {
        return $this->hasMany(PartnerLiquidationMovement::class, ['partner_liquidation_id' => 'partner_liquidation_id']);
    }
        
        
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();            
            return true;
        } else {
            return false;
        }     
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
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
     * Weak relations: PartnerDistributionModel.
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
