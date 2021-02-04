<?php

namespace app\modules\checkout\models;

use app\components\workflow\WithWorkflow;
use webvimark\modules\UserManagement\models\User;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Customer;
use app\modules\sale\models\ProductToInvoice;
use Yii;

/**
 * This is the model class for table "payment_plan".
 *
 * @property integer $payment_plan_id
 * @property string $from_date
 * @property string $status
 * @property integer $fee
 * @property double $original_amount
 * @property double $final_amount
 * @property integer $apply
 * @property double $value_applied
 * @property double $payment_plan_amount
 * @property integer $customer_id
 *
 * @property Customer $customer
 * @property ProductToInvoice[] $productToInvoices
 */
class PaymentPlan extends \app\components\db\ActiveRecord
{

    use WithWorkflow;

    const STATUS_COMPLETED  = 'completed';
    const STATUS_ACTIVE     = 'active';
    const STATUS_CANCELED   = 'canceled';
    
    public $balance;
    public $bill_id;
    public $create_bill;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_plan';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'created_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_date',  'fee', 'original_amount', 'final_amount', 'customer_id', 'payment_plan_amount'], 'required'],
            [['from_date', 'customer', 'create_bill'], 'safe'],
            [['from_date'], 'date'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => ['active', 'completed', 'canceled']],
            [['status'], 'default', 'value' => 'active'],
            [['fee', 'apply', 'customer_id', 'created_by'], 'integer'],
            [['original_amount', 'final_amount', 'value_applied', 'payment_plan_amount'], 'number'],
            [['from_date'], 'date'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_plan_id' => Yii::t('app', 'Payment Plan ID'),
            'from_date' => Yii::t('app', 'From Date'),
            'status' => Yii::t('app', 'Status'),
            'fee' => Yii::t('app', 'Fees'),
            'original_amount' => Yii::t('app', 'Original Amount'),
            'final_amount' => Yii::t('app', 'Final Amount'),
            'apply' => Yii::t('app', 'Apply'),
            'value_applied' => Yii::t('app', 'Value Applied'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'customer' => Yii::t('app', 'Customer'),
            'productToInvoices' => Yii::t('app', 'ProductToInvoices'),
            'payment_plan_amount' => Yii::t('app', 'Payment Plan Amount'),
            'balance' => Yii::t('app', 'payment_plan_balance'),
            'created_at' => Yii::t('app', 'Creado'),
            'created_by' => Yii::t('app', 'Creado por'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductToInvoices()
    {
        return $this->hasMany(ProductToInvoice::className(), ['payment_plan_id' => 'payment_plan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();            
            return true;
        } else {
            return false;
        }     
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        $this->setBalance();
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date);
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        }
    
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Customer, ProductToInvoices.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Retorna el atributo que maneja el estado del objeto para el workflow.
     *
     * @return mixed
     */
    public function getWorkflowAttr()
    {
        return 'status';
    }

    /**
     * Retorna los estados.
     *
     * @return mixed
     */
    public function getWorkflowStates()
    {
        return [
            self::STATUS_ACTIVE => [
                self::STATUS_COMPLETED,
                self::STATUS_CANCELED,
            ],
            self::STATUS_CANCELED => [
                self::STATUS_ACTIVE,
            ],
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}

    public function cancel()
    {
        if($this->can(PaymentPlan::STATUS_CANCELED)) {
            $this->status = PaymentPlan::STATUS_CANCELED;
            $this->updateAttributes(['status']);
            return true;
        }
        return false;
    }
    
    public function setBalance(){
        $balance= $this->final_amount;
        
        $shares= ProductToInvoice::findAll(['payment_plan_id' => $this->payment_plan_id, 'status'=> 'consumed']);
        
        foreach ($shares as $share) {
            $balance= $balance - $share->amount;
}
        
        $this->balance= $balance;
    }
}
