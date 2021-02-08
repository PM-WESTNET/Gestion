<?php

namespace app\modules\firstdata\models;

use Yii;
use app\components\db\ActiveRecord;
use app\modules\sale\models\Company;

/**
 * This is the model class for table "firstdata_company_config".
 *
 * @property int $firstdata_company_config_id
 * @property int $commerce_number
 * @property int $company_id
 *
 * @property FirstdataAutomaticDebit[] $firstdataAutomaticDebits
 * @property Company $company
 * @property FirstdataExport[] $firstdataExports
 */
class FirstdataCompanyConfig extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_company_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commerce_number', 'company_id'], 'required'],
            [['commerce_number', 'company_id'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'company_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_company_config_id' => Yii::t('app', 'Firstdata Company Config ID'),
            'commerce_number' => Yii::t('app', 'Commerce Number'),
            'company_id' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataAutomaticDebits()
    {
        return $this->hasMany(FirstdataAutomaticDebit::className(), ['company_config_id' => 'firstdata_company_config_id']);
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
    public function getFirstdataExports()
    {
        return $this->hasMany(FirstdataExport::className(), ['firstdata_config_id' => 'firstdata_company_config_id']);
    }
}
