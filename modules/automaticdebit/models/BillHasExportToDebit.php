<?php

namespace app\modules\automaticdebit\models;

use app\modules\sale\models\Bill;
use Yii;

/**
 * This is the model class for table "bill_has_export_to_debit".
 *
 * @property int $bill_has_export_to_debit
 * @property int $bill_id
 * @property int $direct_debit_export_id
 *
 * @property Bill $bill
 * @property DirectDebitExport $directDebitExport
 */
class BillHasExportToDebit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bill_has_export_to_debit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'direct_debit_export_id'], 'integer'],
            [['bill_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_id' => 'bill_id']],
            [['direct_debit_export_id'], 'exist', 'skipOnError' => true, 'targetClass' => DirectDebitExport::className(), 'targetAttribute' => ['direct_debit_export_id' => 'direct_debit_export_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_has_export_to_debit' => Yii::t('app', 'Bill Has Export To Debit'),
            'bill_id' => Yii::t('app', 'Bill ID'),
            'direct_debit_export_id' => Yii::t('app', 'Direct Debit Export ID'),
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
    public function getDirectDebitExport()
    {
        return $this->hasOne(DirectDebitExport::className(), ['direct_debit_export_id' => 'direct_debit_export_id']);
    }
}
