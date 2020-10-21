<?php

namespace app\modules\firstdata\models;

use Yii;

/**
 * This is the model class for table "firstdata_import".
 *
 * @property int $firstdata_import_id
 * @property int $presentation_date
 * @property int $created_at
 * @property string $status
 * @property string $response_file
 * @property string $observation_file
 *
 * @property FirstdataImportPayment[] $firstdataImportPayments
 */
class FirstdataImport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_import';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['presentation_date', 'created_at', 'status', 'response_file'], 'required'],
            [['presentation_date', 'created_at'], 'integer'],
            [['status'], 'string'],
            [['response_file', 'observation_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_import_id' => Yii::t('app', 'Firstdata Import ID'),
            'presentation_date' => Yii::t('app', 'Presentation Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'status' => Yii::t('app', 'Status'),
            'response_file' => Yii::t('app', 'Response File'),
            'observation_file' => Yii::t('app', 'Observation File'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataImportPayments()
    {
        return $this->hasMany(FirstdataImportPayment::className(), ['firstdata_import_id' => 'firstdata_import_id']);
    }
}
