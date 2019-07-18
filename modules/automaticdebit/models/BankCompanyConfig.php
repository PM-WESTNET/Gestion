<?php

namespace app\modules\automaticdebit\models;

use app\components\companies\ActiveRecord;
use app\modules\sale\models\Company;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "bank_company_config".
 *
 * @property int $bank_company_config_id
 * @property string $company_identification
 * @property string $branch
 * @property string $control_digit
 * @property string $account_number
 * @property int $company_id
 * @property int $bank_id
 * @property int $created_at
 * @property int $updated_at
 * @property string $service_code
 * @property string $other_service_code
 * @property string $other_company_identification
 *
 * @property Bank $bank
 * @property Company $company
 */
class BankCompanyConfig extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_company_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'bank_id'], 'required'],
            [['company_id', 'bank_id', 'created_at', 'updated_at'], 'integer'],
            [['control_digit'], 'string', 'length' => 2],
            [['company_identification', 'branch', 'account_number', 'service_code', 'other_service_code', 'other_company_identification'], 'string', 'max' => 45],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bank::class, 'targetAttribute' => ['bank_id' => 'bank_id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'company_id']],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ]
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bank_company_config_id' => Yii::t('app', 'Bank Company Config ID'),
            'company_identification' => Yii::t('app', 'Company Identification'),
            'branch' => Yii::t('app', 'Branch'),
            'control_digit' => Yii::t('app', 'Control Digit'),
            'account_number' => Yii::t('app', 'Account Number'),
            'company_id' => Yii::t('app', 'Company ID'),
            'bank_id' => Yii::t('app', 'Bank ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'service_code' => Yii::t('app', 'Service code'),
            'other_service_code' => Yii::t('app', 'Other service code'),
            'other_company_identification' => Yii::t('app', 'Other company identification')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::class, ['bank_id' => 'bank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }
}
