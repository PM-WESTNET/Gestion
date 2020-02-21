<?php

namespace app\modules\employee\models;

use app\components\db\ActiveRecord;
use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\CountableInterface;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\checkout\models\PaymentMethod;
use app\modules\partner\models\PartnerDistributionModel;
use Codeception\Util\Debug;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "employee_payment".
 *
 * @property integer $employee_payment_id
 * @property string $date
 * @property double $amount
 * @property string $description
 * @property integer $employee_id
 * @property integer $timestamp
 * @property double $balance
 * @property string $status;
 * @property integer $partner_distribution_model_id
 * @property integer $company_id
 *
 * @property EmployeeBillHasEmployeePayment[] $employeeBillHasEmployeePayments
 * @property EmployeeBill[] $employeeBills
 * @property Employee $employee
 * @property PartnerDistributionModel $partnerDistributionModel
 * @property EmployeePaymentItem[] $employeePaymentItems
 */
class EmployeePayment extends \app\components\companies\ActiveRecord implements CountableInterface
{

    private $_employeeBills;
    private $_oldBills;

    const STATUS_CREATED = 'created';
    const STATUS_CLOSED = 'closed';
    const STATUS_CONCILED = 'conciled';

    public function __construct($config = array()) {
        parent::__construct($config);

        $this->date = (new \DateTime('now'))->format('d-m-Y');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_payment';
    }

    public function behaviors()
    {
        return [
            'account' => [
                'class'=> 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $statuses = ['created', 'closed', 'tabulated', 'conciled'];

        return [
            [['date', 'employeeBills', 'employee', 'company_id'], 'safe'],
            [['date'], 'date'],
            [['balance'], 'number'],
            [['status'], 'string'],
            [['employee_id'], 'required'],
            [['employee_id', 'timestamp','partner_distribution_model_id', 'company_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => $statuses],
            [['status'], 'default', 'value' => 'created'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_payment_id' => Yii::t('app', 'Employee Payment'),
            'date' => Yii::t('app', 'Date'),
            'amount' => Yii::t('app', 'Amount'),
            'description' => Yii::t('app', 'Observations') . ' ' . Yii::t('app', '(optional)'),
            'employee_id' => Yii::t('app', 'Employee'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'balance' => Yii::t('app', 'Balance'),
            'employeeBillHasEmployeePayments' => Yii::t('app', 'EmployeeBillHasEmployeePayments'),
            'employeeBills' => Yii::t('app', 'Employee Bills'),
            'employee' => Yii::t('app', 'Employee'),
            'status' => Yii::t('app', 'Status'),
            'employee_bill_id' => Yii::t('app', 'Employee Bill'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBillHasEmployeePayments()
    {
        return $this->hasMany(EmployeeBillHasEmployeePayment::class, ['employee_payment_id' => 'employee_payment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBills()
    {
        return $this->hasMany(EmployeeBill::class, ['employee_bill_id' => 'employee_bill_id'])->viaTable('employee_bill_has_employee_payment', ['employee_payment_id' => 'employee_payment_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['employee_id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::className(), ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    public function getEmployeePaymentItems()
    {
        return $this->hasMany(EmployeePaymentItem::className(), ['employee_payment_id' => 'employee_payment_id']);
    }

    /**
     * @brief Sets EmployeeBills relation on helper variable and handles events insert and update
     */
    public function setEmployeeBills($employeeBills){

        if(empty($employeeBills)){
            $employeeBills = [];
        }

        $this->_employeeBills = $employeeBills;

        $saveEmployeeBills = function($event){
            $this->unlinkAll('employeeBills', true);

            foreach ($this->_employeeBills as $id) {
                $this->link('employeeBills', EmployeeBill::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveEmployeeBills);
        $this->on(self::EVENT_AFTER_UPDATE, $saveEmployeeBills);
    }
    
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();
            $this->balance = $this->amount - $this->calculateTotalPayed();
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
    }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate((!$this->date ? (new \DateTime('now'))->format('d-m-Y'): $this->date ), 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        if($this->status == EmployeePayment::STATUS_CLOSED) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * Indica si el modelo puede actualizarse.
     */
    public function getUpdatable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }
        if($this->status == EmployeePayment::STATUS_CLOSED) {
            return false;
        }

        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: EmployeeBillHasEmployeePayments, EmployeeBills, PaymentMethod, Employee.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('employeeBills', true);
        $this->unlinkAll('employeePaymentItems', true);
        if($this->status != 'created' ){
            AccountMovementRelationManager::delete($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->_oldBills = $this->getEmployeeBills()->all();
                /*if ($this->paycheck != null) {
                    $this->paycheck->revertState();
                }*/
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $this->updateBalanceBill(false);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->updateBalanceBill();
    }

    /**
     * Actualiza el balance de las facturas
     * @param bool|true $update
     */
    public function updateBalanceBill($update=true)
    {
        $bills = ($update ? $this->getEmployeeBills()->all() : $this->_oldBills );
        foreach ($bills as $bill) {
            $bill->calculateTotal();
        }
    }

    /**
     * Agrega una factura
     *
     * @param $bill
     * @return EmployeeBillHasEmployeePayment|null|static
     */
    public function addBill($bill)
    {
        if($bill['employee_bill_id']){
            $pay = EmployeeBillHasEmployeePayment::findOne([
                'employee_bill_id'=> $bill['employee_bill_id'],
                'employee_payment_id'=> $bill['employee_payment_id']]);
        }
        if(empty($pay)) {
            $pay = new EmployeeBillHasEmployeePayment();
        }
        $pay->setAttributes($bill);
        $pay->save();

        return $pay;
    }

    /**
     * Agrega una factura
     *
     * @param $bill
     * @return EmployeeBillItem|null|static
     */
    public function addItem($item)
    {
        if($item['employee_payment_item_id']){
            $itemDb = EmployeePaymentItem::findOne([
                'employee_payment_item_id'=> $item['employee_payment_item_id']]);
        }

        if(empty($itemDb)) {
            $itemDb = new EmployeePaymentItem();
            $itemDb->setAttributes($item);
            $itemDb->validate();
            $itemDb->save();
        } else {
            $itemDb->save();
        }

        $this->calculateTotal();

        return $itemDb;
    }


    /**
     * Calcula el importe total del pago en base a los items.
     *
     * @return float
     */
    public function calculateTotal()
    {
        $amount = 0;
        foreach( $this->getEmployeePaymentItems()->all()  as $item) {
            $amount += $item->amount;
        }
        $this->updateAttributes(['amount'=>round($amount,2)]);
        return $amount;
    }

    /**
     * Calcula el importe total del pago
     *
     * @return float
     */
    public function calculateTotalPayed()
    {
        $amount = 0;
        foreach( $this->getEmployeeBillHasEmployeePayments()->all()  as $bill) {
            $amount += ($bill->amount * $bill->employeeBill->billType->multiplier);
        }
        return round($amount,2);
    }

    public function getConfig()
    {
        $query = PaymentMethod::find();
        $query->select(['payment_method_id', 'name']);
        $paymentMethods = ArrayHelper::map($query->asArray()->all(), 'payment_method_id', 'name');

        $paymentMethods['total'] = 'Total';
        return $paymentMethods;
    }

    public function getAmounts()
    {
        $paymentMethods = [];
        $paymentMethods['total'][] = $this->amount;
        foreach($this->employeePaymentItems as $item) {
            $paymentMethods[$item->payment_method_id][] = $item->amount;
        }
        return $paymentMethods;
    }

    /**
     * Verifica si se puede cerrar o no el pago.
     */
    public function canClose()
    {
        $total = round($this->calculateTotal());
        $totalPayed = round($this->calculateTotalPayed());
        return $this->status == 'created' && ( ($total == $totalPayed || $totalPayed==0) && $total != 0  );
    }

    public function verifyItems($newDate=null)
    {
        $modelDate = new \DateTime(($newDate ? $newDate : $this->date ));
        error_log( $modelDate->format('Y-m-d') . " <= " );
        /** @var EmployeePaymentItem $employeePaymentItem */
        foreach ($this->employeePaymentItems as $employeePaymentItem) {
            if($employeePaymentItem->moneyBoxAccount->small_box)  {
                $date = new \DateTime($employeePaymentItem->moneyBoxAccount->daily_box_last_closing_date);

                if($modelDate <= $date ) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     *  Aplica un employee_payment a una o mas employee_bills, de la mas vieja a la mas nueva
     * y actualiza el valor de balance
     */
    public function associateEmployeeBills($employee_bill_ids)
    {
        $return = true;
        if (count($employee_bill_ids) > 0) {
            $employee_bills = EmployeeBill::find()->where(['employee_bill_id' => $employee_bill_ids])->orderBy(['date' => SORT_ASC])->all();
            $saldo = $this->balance;
            foreach ($employee_bills as $employee_bill) {
                if ($saldo > 0) {
                    $debt = $employee_bill->getDebt();

                    $pbhpp = new EmployeeBillHasEmployeePayment([
                        'employee_bill_id' => $employee_bill->employee_bill_id,
                        'employee_payment_id' => $this->employee_payment_id
                    ]);

                    if ($saldo >= $debt) {
                        $pbhpp->amount = $debt;
                        $saldo -= $debt;
                    } else if ($saldo < $debt) {
                        $pbhpp->amount = $saldo;
                        $saldo = 0;
                    }

                    if(!$pbhpp->save()){
                        $return = false;
                    }
                }
            }
        }
        //Actualizamos el balance
        $this->updateAttributes(['balance' => $this->amount - $this->calculateTotalPayed()]);

        return $return;
    }

    /**
     * Elimina la asociación con employee bills
     */
    public function disassociateEmployeeBills($employee_bill_ids)
    {
        if (count($employee_bill_ids) > 0) {
            EmployeeBillHasEmployeePayment::deleteAll(['employee_payment_id' => $this->employee_payment_id, 'employee_bill_id' => $employee_bill_ids]);
            //Actualizamos el balance
            $this->updateAttributes(['balance' => $this->amount - $this->calculateTotalPayed()]);
            return true;
        }
    }
}