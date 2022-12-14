<?php

namespace app\modules\automaticdebit\models;

use app\components\db\ActiveRecord;
use app\modules\sale\models\Customer;
use Yii;
use yii\behaviors\TimestampBehavior;
use app\components\user\User;
/**
 * This is the model class for table "automatic_debit".
 *
 * @property int $automatic_debit_id
 * @property int $customer_id
 * @property int $bank_id
 * @property string $cbu
 * @property string $beneficiario_number
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $customer_type
 * @property int $user_id
 *
 * @property Bank $bank
 * @property Customer $customer
 */
class AutomaticDebit extends ActiveRecord
{
    const ENABLED_STATUS = 10;
    const DISABLED_STATUS = 31;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'automatic_debit';
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
            [['customer_id', 'bank_id', 'cbu', 'status'], 'required'],
            [['customer_id', 'bank_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cbu', 'beneficiario_number'], 'string', 'length' => 22],
            [['beneficiario_number'], 'string', 'length' => 22], 
            // so, we use a regex to match any number and then accept the input as a string (numbers only).
            [['cbu'], 'match', 'pattern' => '/^[0-9]{22}$/'], // this will invalidate any input that isnt a number with length 22
            // 9999999999999999999999
            [['customer_type'], 'string'],
            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bank::class, 'targetAttribute' => ['bank_id' => 'bank_id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['user_id'],'integer'],
            //[['user_id'],'required'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'automatic_debit_id' => Yii::t('app', 'Automatic Debit ID'),
            'customer_id' => Yii::t('app', 'Customer'),
            'bank_id' => Yii::t('app', 'Bank ID'),
            'cbu' => Yii::t('app', 'Cbu'),
            'beneficiario_number' => Yii::t('app', 'Beneficiario Number'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'customer_type' => Yii::t('app', 'Customer Type'),
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
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    public function getStatusLabel()
    {
        $labels = [
            self::ENABLED_STATUS => Yii::t('app','Active'),
            self::DISABLED_STATUS => Yii::t('app','Inactive')
        ];

        return $labels[$this->status];
    }

    public function getUser() 
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {

        if ($insert) {
            if (!empty($this->customer_id)) {
                $customer = Customer::findOne($this->customer_id);

                if ($customer) {
                    $this->beneficiario_number = str_pad($customer->code, 22, '0', STR_PAD_LEFT);
                } else{
                    return false;
                }

            }else {
                return false;
            }
            $this->user_id = $_SESSION['__id'];
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
