<?php

namespace app\modules\westnet\reports\models;

use Yii;
use app\components\db\ActiveRecord;
use app\modules\sale\models\Customer;
use webvimark\modules\UserManagement\models\User;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer_update_register".
 *
 * @property int $customer_update_register_id
 * @property int $customer_id
 * @property int $user_id
 * @property int $date
 *
 * @property Customer $customer
 * @property User $user
 */
class CustomerUpdateRegister extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_update_register';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'user_id'], 'required'],
            [['customer_id', 'user_id', 'date'], 'integer'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'date' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date']
                ],
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_update_register_id' => Yii::t('app', 'Customer Update Register ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function createRegister($customer_id) 
    {
        $model = new CustomerUpdateRegister([
            'customer_id' => $customer_id,
            'user_id' => Yii::$app->user->id 
        ]);

        $model->save();
    }
}
