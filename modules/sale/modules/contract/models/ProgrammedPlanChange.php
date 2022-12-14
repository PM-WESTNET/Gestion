<?php

namespace app\modules\sale\modules\contract\models;

use app\modules\sale\models\Product;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "programmatic_change_plan".
 *
 * @property int $programmatic_change_plan_id
 * @property int $date
 * @property int $applied
 * @property int $created_at
 * @property int $updated_at
 * @property int $contract_id
 * @property int $product_id
 * @property int $user_id
 *
 * @property Contract $contract
 * @property Product $product
 * @property User $user
 */
class ProgrammedPlanChange extends \yii\db\ActiveRecord
{

    public $customer_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'programmed_plan_change';
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'contract_id', 'product_id', 'user_id'], 'required'],
            [['created_at', 'updated_at', 'contract_id', 'product_id', 'user_id'], 'integer'],
            [['applied'], 'boolean'],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::class, 'targetAttribute' => ['contract_id' => 'contract_id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'product_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            ['date', 'validateDate'],
            ['applied' , 'default', 'value' => 0]
        ];
    }

    public function validateDate()
    {
        if ($this->date && strtotime(Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd')) <= strtotime(date('Y-m-d'))) {
            $this->addError('date', Yii::t('app','Date must be greather than current date'));
            return false;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'programmatic_change_plan_id' => Yii::t('app', 'Programmatic Change Plan ID'),
            'date' => Yii::t('app', 'Date'),
            'applied' => Yii::t('app', 'Applied'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'contract_id' => Yii::t('app', 'Contract'),
            'product_id' => Yii::t('app', 'Plan'),
            'user_id' => Yii::t('app', 'User'),
            'customer_id' => Yii::t('app', 'Customer'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::class, ['contract_id' => 'contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        $this->formatDatesBeforeSave();
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->formatDatesAfterFind();
    }

    public function formatDatesBeforeSave()
    {
        if ($this->date) {
            $this->date = strtotime(Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd'));
        }
    }

    public function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'dd-MM-yyyy');
    }

    public function getDeletable()
    {
        if($this->applied) {
            return false;
        }

        return true;
    }
}
