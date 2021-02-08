<?php

namespace app\modules\firstdata\models;

use Yii;
use app\components\db\ActiveRecord;

/**
 * This is the model class for table "firstdata_debit_has_export".
 *
 * @property int $firstdata_debit_has_export_id
 * @property int $firstdata_automatic_debit_id
 * @property int $firstdata_export_id
 *
 * @property FirstdataAutomaticDebit $firstdataAutomaticDebit
 * @property FirstdataExport $firstdataExport
 */
class FirstdataDebitHasExport extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_debit_has_export';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_automatic_debit_id', 'firstdata_export_id'], 'required'],
            [['firstdata_automatic_debit_id', 'firstdata_export_id'], 'integer'],
            [['firstdata_automatic_debit_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataAutomaticDebit::className(), 'targetAttribute' => ['firstdata_automatic_debit_id' => 'firstdata_automatic_debit_id']],
            [['firstdata_export_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataExport::className(), 'targetAttribute' => ['firstdata_export_id' => 'firstdata_export_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_debit_has_export_id' => Yii::t('app', 'Firstdata Debit Has Export ID'),
            'firstdata_automatic_debit_id' => Yii::t('app', 'Firstdata Automatic Debit ID'),
            'firstdata_export_id' => Yii::t('app', 'Firstdata Export ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataAutomaticDebit()
    {
        return $this->hasOne(FirstdataAutomaticDebit::className(), ['firstdata_automatic_debit_id' => 'firstdata_automatic_debit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataExport()
    {
        return $this->hasOne(FirstdataExport::className(), ['firstdata_export_id' => 'firstdata_export_id']);
    }
}
