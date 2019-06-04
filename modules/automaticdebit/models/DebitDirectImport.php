<?php

namespace app\modules\automaticdebit\models;

use Yii;

/**
 * This is the model class for table "debit_direct_import".
 *
 * @property int $debit_direct_import_id
 * @property string $file
 * @property int $import_timestamp
 * @property int $process_timestamp
 * @property int $status
 * @property int $company_id
 * @property int $bank_id
 *
 * @property Bank $bank
 * @property Company $company
 * @property DebitDirectImportHasPayment[] $debitDirectImportHasPayments
 */
class DebitDirectImport extends \yii\db\ActiveRecord
{
    const DRAFT_STATUS = 1;
    const SUCCESS_STATUS = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'debit_direct_import';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['import_timestamp', 'process_timestamp', 'status', 'company_id', 'bank_id'], 'integer'],
            [['company_id', 'bank_id'], 'required'],
            [['file'], 'string', 'max' => 255],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bank::className(), 'targetAttribute' => ['bank_id' => 'bank_id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'company_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'debit_direct_import_id' => Yii::t('app', 'Debit Direct Import ID'),
            'file' => Yii::t('app', 'File'),
            'import_timestamp' => Yii::t('app', 'Import Timestamp'),
            'process_timestamp' => Yii::t('app', 'Process Timestamp'),
            'status' => Yii::t('app', 'Status'),
            'company_id' => Yii::t('app', 'Company ID'),
            'bank_id' => Yii::t('app', 'Bank ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
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
    public function getDebitDirectImportHasPayments()
    {
        return $this->hasMany(DebitDirectImportHasPayment::className(), ['debit_direct_import_id' => 'debit_direct_import_id']);
    }
}
