<?php

namespace app\modules\sale\models;

use app\modules\log\db\ActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer_company_history".
 *
 * @property int $customer_company_history_id
 * @property int $customer_id
 * @property int $old_company_id
 * @property int $new_company_id
 * @property int $created_at
 *
 * @property Customer $customer
 * @property Company $newCompany
 * @property Company $oldCompany
 */
class CustomerCompanyHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_company_history';
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'timestamp'=>[ 
                    'class'=>TimestampBehavior::class,
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => [
                            'created_at'
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'old_company_id', 'new_company_id', 'created_at'], 'integer'],
            [['old_company_id', 'new_company_id' ], 'required'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['new_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['new_company_id' => 'company_id']],
            [['old_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['old_company_id' => 'company_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_company_history_id' => Yii::t('app', 'Customer Company History ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'old_company_id' => Yii::t('app', 'Old Company ID'),
            'new_company_id' => Yii::t('app', 'New Company ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'new_company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOldCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'old_company_id']);
    }
}
