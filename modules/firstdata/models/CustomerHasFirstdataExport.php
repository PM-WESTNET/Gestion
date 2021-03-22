<?php

namespace app\modules\firstdata\models;

use Yii;
use app\components\db\ActiveRecord;

/**
 * This is the model class for table "customer_has_firstdata_export".
 *
 * @property int $customer_has_firstdata_export_id
 * @property int $customer_id
 * @property int $firstdata_export_id
 * @property string $month
 *
 * @property Customer $customer
 * @property FirstdataExport $firstdataExport
 */
class CustomerHasFirstdataExport extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_has_firstdata_export';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'firstdata_export_id'], 'integer'],
            [['month'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['firstdata_export_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataExport::className(), 'targetAttribute' => ['firstdata_export_id' => 'firstdata_export_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_has_firstdata_export_id' => Yii::t('app', 'Customer Has Firstdata Export ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'firstdata_export_id' => Yii::t('app', 'Firstdata Export ID'),
            'month' => Yii::t('app', 'Month'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataExport()
    {
        return $this->hasOne(FirstdataExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }
}
