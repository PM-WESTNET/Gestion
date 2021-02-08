<?php

namespace app\modules\partner\models;

use app\modules\accounting\models\AccountMovement;
use app\modules\provider\models\ProviderBill;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Company;
use Yii;

/**
 * This is the model class for table "partner_distribution_model".
 *
 * @property integer $partner_distribution_model_id
 * @property string $name
 * @property integer $company_id
 *
 * @property AccountMovement[] $accountMovements
 * @property Bill[] $bills
 * @property Company[] $companies
 * @property Company $company
 * @property PartnerDistributionModelHasPartner[] $partnerDistributionModelHasPartner
 * @property ProviderBill[] $providerBills
 */
class PartnerDistributionModel extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'partner_distribution_model';
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
            [['name', 'company_id'], 'required'],
            [['company_id'], 'integer'],
            [['company'], 'safe'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'partner_distribution_model_id' => Yii::t('partner', 'Partner Distribution Model'),
            'name' => Yii::t('app', 'Name'),
            'company_id' => Yii::t('app', 'Company'),
            'accountMovements' => Yii::t('accounting', 'Account Movements'),
            'bills' => Yii::t('app', 'Bills'),
            'companies' => Yii::t('app', 'Companies'),
            'company' => Yii::t('app', 'Company'),
            'partnerHasCompanies' => Yii::t('partner', 'PartnerHasCompanies'),
            'providerBills' => Yii::t('app', 'Provider Bills'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountMovements()
    {
        return $this->hasMany(AccountMovement::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(Bill::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModelHasPartner()
    {
        return $this->hasMany(PartnerDistributionModelHasPartner::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProviderBills()
    {
        return $this->hasMany(ProviderBill::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
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
     * Weak relations: AccountMovements, Bills, Companies, Company, PartnerHasCompanies, ProviderBills.
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

    public function getTotalPercentage($partner_distribution_model_has_partner_exclude = null)
    {
        $total = 0;
        foreach( $this->partnerDistributionModelHasPartner as $key=>$value ) {
            $total += ( $partner_distribution_model_has_partner_exclude == null &&
                        $value->partner_distribution_model_has_partner_id != $partner_distribution_model_has_partner_exclude ? $value->percentage : 0  );
        }
        return $total;
    }
}
