<?php

namespace app\modules\sale\models;

use app\components\workflow\WithWorkflow;
use app\modules\checkout\models\PaymentPlan;
use app\modules\sale\modules\contract\models\ContractDetail;
use Yii;

/**
 * This is the model class for table "product_to_invoice".
 *
 * @property integer $product_to_invoice_id
 * @property integer $contract_detail_id
 * @property integer $funding_plan_id
 * @property string $date
 * @property string $period
 * @property double $amount
 * @property string $status
 * @property integer $timestamp
 * @property string $description
 * @property integer $discount_id
 * @property integer $payment_plan_id
 * @property integer $customer_id
 * @property integer $qty
 *
 * @property ContractDetail $contractDetail
 * @property Discount $discount
 * @property FundingPlan $fundingPlan
 * @property PaymentPlan $paymentPlan
 * @property Customer $customer
 */
class ProductToInvoice extends \app\components\db\ActiveRecord
{
    use WithWorkflow;

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_CONSUMED = 'consumed';
    const STATUS_CANCELED = 'canceled';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_to_invoice';
    }

    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_detail_id', 'funding_plan_id', 'timestamp', 'discount_id', 'payment_plan_id', 'customer_id', 'qty'], 'integer'],
            [['date', 'period', 'contractDetail', 'discount', 'fundingPlan', 'paymentPlan', 'customer'], 'safe'],
            [['date', 'period'], 'date'],
            [['amount'], 'number'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => ['draft', 'active', 'consumed', 'canceled']],
            [['status'], 'default', 'value' => 'draft'],
            [['qty'], 'default', 'value' => '1'],
            [['description'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_to_bill_id' => Yii::t('app', 'Product To Bill ID'),
            'contract_detail_id' => Yii::t('app', 'Contract Detail'),
            'funding_plan_id' => Yii::t('app', 'Funding Plan'),
            'date' => Yii::t('app', 'Date'),
            'period' => Yii::t('app', 'Period'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'contractDetail' => Yii::t('app', 'Contract Detail'),
            'fundingPlan' => Yii::t('app', 'Funding Plan'),
            'discount' => Yii::t('app', 'Discount'),
            'paymentPlan' => Yii::t('app', 'Payment Plan'),
            'customer' => Yii::t('app', 'Customer'),
            'qty' => Yii::t('app', 'Qty'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractDetail()
    {
        return $this->hasOne(ContractDetail::className(), ['contract_detail_id' => 'contract_detail_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundingPlan()
    {
        return $this->hasOne(FundingPlan::className(), ['funding_plan_id' => 'funding_plan_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discount::className(), ['discount_id' => 'discount_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentPlan()
    {
        return $this->hasOne(PaymentPlan::className(), ['payment_plan_id' => 'payment_plan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
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
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
        $this->period = Yii::$app->formatter->asDate($this->period);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        $this->period = Yii::$app->formatter->asDate($this->period, 'yyyy-MM-dd');
        $this->timestamp = (new \DateTime)->getTimestamp();
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
     * Weak relations: ContractDetail, FundingPlan.
     */
    protected function unlinkWeakRelations()
    {
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
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
        return "status";
    }

    /**
     * Retorna los estados.
     *
     * @return mixed
     */
    public function getWorkflowStates()
    {
        return [
            self::STATUS_DRAFT => [
                self::STATUS_ACTIVE,
                self::STATUS_CANCELED,
            ],
            self::STATUS_ACTIVE => [
                self::STATUS_CONSUMED,
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
}
