<?php

namespace app\modules\firstdata\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use app\modules\firstdata\components\FirstdataImport as Import;

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

    public $response;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_import';
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at']
                ]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['response_file', 'money_box_account_id'], 'required'],
            [['presentation_date', 'created_at'], 'integer'],
            [['status'], 'string'],
            [['response', 'total', 'registers'],  'safe'],
            [['response'], 'file', 'extensions' => 'txt'],
            [['response_file', 'observation_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_import_id' => Yii::t('app', 'Firstdata Import'),
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

    public function uploadFiles()
    {
        if (!$this->validate('response')) {
            return false;
        }

        $response = UploadedFile::getInstance($this, 'response');
        $filepath = Yii::getAlias('@app').'/web/firstdata_imports/'.$this->firstdata_import_id;
        
        if (!file_exists($filepath)) {
            mkdir($filepath, 0777);
        }
        
        $filename = $filepath . '/'. $response->name . '.'. $response->extension;
        
        $response->saveAs($filename);
        
        $this->response_file = $filename;


        return true;

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Import::processFile($this);
    }
}
