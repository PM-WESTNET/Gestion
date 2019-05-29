<?php

namespace app\modules\automaticdebit\models;

use Yii;

/**
 * This is the model class for table "direct_debit_export".
 *
 * @property int $direct_debit_export_id
 * @property string $file
 * @property int $create_timestamp
 * @property int $bank_id
 * @property int $company_id
 *
 * @property BillHasExportToDebit[] $billHasExportToDebits
 * @property Bank $bank
 * @property Company $company
 */
class DirectDebitExport extends \yii\db\ActiveRecord
{
    public $from_date;
    public $to_date;
    public $debit_date;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'direct_debit_export';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id'], 'required'],
            [['create_timestamp', 'bank_id', 'company_id'], 'integer'],
            [['file'], 'string', 'max' => 255],
            [['from_date', 'to_date', 'debit_date'], 'safe'],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bank::className(), 'targetAttribute' => ['bank_id' => 'bank_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'direct_debit_export_id' => Yii::t('app', 'Direct Debit Export ID'),
            'file' => Yii::t('app', 'File'),
            'create_timestamp' => Yii::t('app', 'Create Timestamp'),
            'bank_id' => Yii::t('app', 'Bank ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillHasExportToDebits()
    {
        return $this->hasMany(BillHasExportToDebit::className(), ['direct_debit_export_id' => 'direct_debit_export_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }
}
