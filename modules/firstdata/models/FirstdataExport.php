<?php

namespace app\modules\firstdata\models;

use Yii;

/**
 * This is the model class for table "firstdata_export".
 *
 * @property int $firstdata_export_id
 * @property int $created_at
 * @property string $file_url
 * @property int $firstdata_config_id
 *
 * @property BillHasFirstdataExport[] $billHasFirstdataExports
 * @property FirstdataDebitHasExport[] $firstdataDebitHasExports
 * @property FirstdataCompanyConfig $firstdataConfig
 */
class FirstdataExport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_export';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'file_url', 'firstdata_config_id'], 'required'],
            [['created_at', 'firstdata_config_id'], 'integer'],
            [['file_url'], 'string', 'max' => 255],
            [['firstdata_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataCompanyConfig::className(), 'targetAttribute' => ['firstdata_config_id' => 'firstdata_company_config_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_export_id' => Yii::t('app', 'Firstdata Export ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'file_url' => Yii::t('app', 'File Url'),
            'firstdata_config_id' => Yii::t('app', 'Firstdata Config ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillHasFirstdataExports()
    {
        return $this->hasMany(BillHasFirstdataExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataDebitHasExports()
    {
        return $this->hasMany(FirstdataDebitHasExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataConfig()
    {
        return $this->hasOne(FirstdataCompanyConfig::className(), ['firstdata_company_config_id' => 'firstdata_config_id']);
    }
}
