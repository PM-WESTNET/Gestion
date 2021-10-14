<?php

namespace app\modules\firstdata\models;

use Yii;
use yii\web\UploadedFile;
use app\components\db\ActiveRecord;
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
 * @property int $firstdata_config_id
 *
 * @property FirstdataImportPayment[] $firstdataImportPayments
 */
class FirstdataImport extends ActiveRecord
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
            [['response_file', 'money_box_account_id', 'firstdata_config_id'], 'required'],
            [['presentation_date', 'created_at', 'firstdata_config_id'], 'integer'],
            [['status'], 'string'],
            [['response', 'total', 'registers'],  'safe'],
            [['response'], 'file', 'extensions' => 'txt'],
            [['response_file', 'observation_file'], 'string', 'max' => 255],
            [['firstdata_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataCompanyConfig::className(), 'targetAttribute' => ['firstdata_config_id' => 'firstdata_company_config_id']],
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
            'firstdata_config_id' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataImportPayments()
    {
        return $this->hasMany(FirstdataImportPayment::className(), ['firstdata_import_id' => 'firstdata_import_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataConfig()
    {
        return $this->hasOne(FirstdataCompanyConfig::className(), ['firstdata_company_config_id' => 'firstdata_config_id']);
    }

    public function uploadFiles()
    {
        if (!$this->validate('response')) {
            return false;
        }

        $response = UploadedFile::getInstance($this, 'response');
        $filepath = Yii::getAlias('@app').'/web/firstdata_imports/'. time();
        
        if (!file_exists($filepath)) {
            mkdir($filepath, 0777);
        }
        
        $filename = $filepath . '/'. $response->name . '.'. $response->extension;
        //$filename = $filepath . '/'. $response->name;

        //var_dump($response->name);die();

        $response->saveAs($filename);
        //$response->saveAs($filename, false);
        
        $this->response_file = $filename;


        return true;

    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Import::processFile($this);
    }
}
