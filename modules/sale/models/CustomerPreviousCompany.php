<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "customer_previous_company".
 *
 * @property int $id
 * @property string $company
 *
 * @property Customer[] $customers
 * @property Customer[] $customers0
 */
class CustomerPreviousCompany extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_previous_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company' => 'Company',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['previous_company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers0()
    {
        return $this->hasMany(Customer::className(), ['previous_company_id' => 'id']);
    }
}
