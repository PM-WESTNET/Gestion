<?php

namespace app\modules\sale\models;

// use app\components\companies\User;

use webvimark\modules\UserManagement\models\User;

use Yii;

/**
 * This is the model class for table "observations".
 *
 * @property int $id
 * @property int $author_id
 * @property int $customer_id
 * @property string $observation
 * @property string $date
 *
 * @property User $author
 * @property Customer $customer
 */
class Observation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'observations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'customer_id'], 'required'],
            [['author_id', 'customer_id'], 'integer'],
            [['date'], 'safe'],
            [['observation'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'customer_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'customer_id' => 'Customer ID',
            'observation' => 'Observation',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }
}
