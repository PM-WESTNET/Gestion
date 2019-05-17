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
            [['parent_company_id', 'company_id', 'percentage'], 'required'],
            [['parent_company_id', 'company_id'], 'integer'],
            [['percentage'], 'number', 'max' => 100 ],
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

    /**
     * @param $parent_company_id
     * @param $company_id
     * @return mixed|string
     * Devuelve el valor de configuracion en porcentaje de una empresa, si no encuentra la configuración devolverá 0
     */
    public static function getCompanyPercentage($company_id) {
        $company = Company::findOne($company_id);

        if(!$company) {
            return '0';
        }

        $percentage = AdsPercentagePerCompany::find()->where(['parent_company_id' => $company->parent_id, 'company_id' => $company->company_id])->one();

        return $percentage ? $percentage->percentage : '0';
    }

    /**
     * @param $parent_company_id
     * @param $company_id
     * @param $percentage_value
     * @return bool
     * Setea el valor de porcentaje par una empresa en particular, si el regustro de la configuración no existiera, creará uno.
     */
    public static function setCompanyPercentage($company_id, $percentage_value) {
        $company = Company::findOne($company_id);

        if(!$company) {
            return false;
        }

        $percentage = AdsPercentagePerCompany::find()->where(['parent_company_id' => $company->parent_id, 'company_id' => $company->company_id])->one();

        if(!$percentage) {
            $percentage = new AdsPercentagePerCompany([
               'parent_company_id' => $company->parent_id,
               'company_id' => $company_id,
               'percentage' => $percentage_value
            ]);
        } else {
            $percentage->percentage = $percentage_value;
        }

        return $percentage->save();
    }
}
