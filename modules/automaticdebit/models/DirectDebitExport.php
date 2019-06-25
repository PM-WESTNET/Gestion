<?php

namespace app\modules\automaticdebit\models;

use app\modules\sale\models\Company;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "direct_debit_export".
 *
 * @property int $direct_debit_export_id
 * @property string $file
 * @property int $create_timestamp
 * @property int $bank_id
 * @property int $company_id
 * @property string $type
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


    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp']
                ],
                'createdAtAttribute' => 'create_timestamp'
            ]
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'type'], 'required'],
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
            'from_date' => Yii::t('app','From Date'),
            'to_date' => Yii::t('app','To Date'),
            'debit_date' => Yii::t('app','Debit Date'),
            'type' => Yii::t('app','Type')
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

    public function generate()
    {
        $bankInstance = $this->bank->getBankInstance();

        $bankInstance->processTimestamp = strtotime(Yii::$app->formatter->asDate($this->debit_date, 'yyyy-MM-dd'));
        $bankInstance->periodFrom = strtotime(Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd'));
        $bankInstance->periodTo = strtotime(Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd'));
        $bankInstance->type = $this->type;

        return $bankInstance->export($this);
    }

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }


}
