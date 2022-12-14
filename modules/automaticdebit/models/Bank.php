<?php

namespace app\modules\automaticdebit\models;

use app\modules\automaticdebit\components\BankInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bank".
 *
 * @property int $bank_id
 * @property string $name
 * @property int $status
 * @property string $class
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AutomaticDebit[] $automaticDebits
 * @property BankCompanyConfig[] $bankCompanyConfigs
 */
class Bank extends \yii\db\ActiveRecord
{

    const STATUS_ENABLED = 10;
    const STATUS_DISABLED = 31;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank';
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ]
        ]); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status', 'class'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['class'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bank_id' => Yii::t('app', 'Bank ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'class' => Yii::t('app', 'Class'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutomaticDebits()
    {
        return $this->hasMany(AutomaticDebit::className(), ['bank_id' => 'bank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBankCompanyConfigs()
    {
        return $this->hasMany(BankCompanyConfig::className(), ['bank_id' => 'bank_id']);
    }

    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_ENABLED => Yii::t('app','Enabled'),
            self::STATUS_DISABLED => Yii::t('app','Disabled')
        ];

        return $labels[$this->status];
    }

    /**
     * @return BankInterface
     * @throws InvalidConfigException
     */
    public function getBankInstance()
    {
        if (empty($this->class)) {
            throw new InvalidConfigException('Bank class can`t be empty');
        }

        $class = $this->class;
        $instance = new $class;

        if (!($instance instanceof BankInterface)) {
            throw new InvalidConfigException('Bank class must implement Bank Interface');
        }

        return $instance;
    }
}
