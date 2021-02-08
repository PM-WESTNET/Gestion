<?php

namespace app\modules\firstdata\models;

use Yii;

/**
 * This is the model class for table "bill_has_firstdata_export".
 *
 * @property int $bill_has_firstdata_export_id
 * @property int $bill_id
 * @property int $firstdata_export_id
 *
 * @property Bill $bill
 * @property FirstdataExport $firstdataExport
 */
class BillHasFirstdataExport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bill_has_firstdata_export';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'firstdata_export_id'], 'required'],
            [['bill_id', 'firstdata_export_id'], 'integer'],
            [['bill_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_id' => 'bill_id']],
            [['firstdata_export_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataExport::className(), 'targetAttribute' => ['firstdata_export_id' => 'firstdata_export_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_has_firstdata_export_id' => Yii::t('app', 'Bill Has Firstdata Export ID'),
            'bill_id' => Yii::t('app', 'Bill ID'),
            'firstdata_export_id' => Yii::t('app', 'Firstdata Export ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bill::className(), ['bill_id' => 'bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataExport()
    {
        return $this->hasOne(FirstdataExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }
}
