<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\sale\models\Company;

/**
 * This is the model class for table "ads_percentage_per_company".
 *
 * @property int $percentage_per_company_id
 * @property int $parent_company_id
 * @property int $company_id
 * @property double $percentage
 *
 * @property Company $company
 * @property Company $parentCompany
 */
class AdsPercentagePerCompany extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ads_percentage_per_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_company_id', 'company_id'], 'integer'],
            [['percentage'], 'number'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'company_id']],
            [['parent_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['parent_company_id' => 'company_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'percentage_per_company_id' => Yii::t('app', 'Percentage Per Company ID'),
            'parent_company_id' => Yii::t('app', 'Parent Company ID'),
            'company_id' => Yii::t('app', 'Company ID'),
            'percentage' => Yii::t('app', 'Percentage'),
        ];
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
    public function getParentCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'parent_company_id']);
    }
}
