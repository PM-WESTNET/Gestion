<?php

namespace app\modules\employee\models;

use app\modules\accounting\components\AccountMovementRelationManager;
use app\modules\accounting\components\CountableInterface;
use app\modules\accounting\components\CountableMovement;
use app\modules\config\models\Config;
use app\modules\partner\models\PartnerDistributionModel;
use app\modules\sale\models\BillType;
use app\modules\sale\models\TaxRate;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "employee_bill".
 *
 * @property integer $employee_bill_id
 * @property string $date
 * @property string $type
 * @property string $number
 * @property double $net
 * @property double $taxes
 * @property double $total
 * @property integer $employee_id
 * @property string $description
 * @property integer $timestamp
 * @property double $balance
 * @property integer $bill_type_id
 * @property string $status
 * @property integer $company_id
 * @property integer $partner_distribution_model_id
 *
 * @property BillType $billType
 * @property Employee $employee
 * @property EmployeeBillHasEmployeePayment[] $employeeBillHasEmployeePayments
 * @property EmployeePayment[] $employeePayments
 * @property EmployeeBillHasTaxRate[] $employeeBillHasTaxRates
 * @property TaxRate[] $taxRates
 * @property EmployeeBillItem[] $employeeBillItems
 * @property PartnerDistributionModel $partnerDistributionModel
 *
 */
class EmployeeBill extends \app\components\companies\ActiveRecord implements CountableInterface
{

    const STATUS_DRAFT = 'draft';
    const STATUS_CLOSED = 'closed';

    //Variable utilizada para generar un pago en caso de que la factura ya haya sido pagada al momento de cargarla
    public $payed;

    //Campos para validación de número de comprobante (atributo number)
    public $number1;
    public $number2;

    private $_employeePayments;
    private $_taxRates;
    
    public function __construct($config = array()) {
        parent::__construct($config);
        
        $this->date = Yii::$app->formatter->asDate(date('Y-m-d'), 'yyyy-MM-dd');
        $this->total = 0;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee_bill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['billType', 'employee', 'date', 'partnerDistributionModel'], 'safe'],
            [['date','bill_type_id', 'employee_id'], 'required'],
            [['net', 'taxes', 'total', 'partner_distribution_model_id'], 'number'],
            [['number', 'status'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 255],
            [['payed'],'boolean'],
            [['status'], 'default', 'value' => EmployeeBill::STATUS_DRAFT],
            [['company_id', 'number1', 'number2'], 'safe'],
            ['number', 'unique', 'targetAttribute' => ['number', 'employee_id', 'bill_type_id']],
            ['number1', 'default' , 'value' => '0000'],
            ['number2', 'default', 'value' => '00000000'],
            ['date', 'validateMinimunDate']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_bill_id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'type' => Yii::t('app', 'Type'),
            'number' => Yii::t('app', 'Number'),
            'net' => Yii::t('app', 'Net amount'),
            'taxes' => Yii::t('app', 'IVA (importe)'),
            'total' => Yii::t('app', 'Total'),
            'employee_id' => Yii::t('app', 'Employee'),
            'description' => Yii::t('app', 'Observations'),
            'payed' => Yii::t('app', 'Bill payed'),
            'timestamp' => Yii::t('app', 'Created at'),
            'bill_type_id' => Yii::t('app', 'Bill Type'),
            'billType' => Yii::t('app', 'Bill Type'),
            'employee' => Yii::t('app', 'Employee'),
            'employeeBillHasEmployeePayments' => Yii::t('app', 'Employee Payments'),
            'employeePayments' => Yii::t('app', 'Employee Payments'),
            'employeeBillHasTaxRates' => Yii::t('app', 'Bill Tax Rates'),
            'taxRates' => Yii::t('app', 'Tax Rates'),
            'status' => Yii::t('app', 'Status'),
            'number1' => Yii::t('app', 'Point of sale'),
            'number2' => Yii::t('app', 'Number')
        ];
    }
    
    public function behaviors()
    {
        return [
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'account' => [
                'class'=> 'app\modules\accounting\behaviors\AccountMovementBehavior'
            ],
            'modifier' => [
                'class'=> 'app\components\db\ModifierBehavior'
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillType()
    {
        return $this->hasOne(BillType::class, ['bill_type_id' => 'bill_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['employee_id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBillHasEmployeePayments()
    {
        return $this->hasMany(EmployeeBillHasEmployeePayment::class, ['employee_bill_id' => 'employee_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeePayments()
    {
        return $this->hasMany(EmployeePayment::class, ['employee_payment_id' => 'employee_payment_id'])->viaTable('employee_bill_has_employee_payment', ['employee_bill_id' => 'employee_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBillHasTaxRates()
    {
        return $this->hasMany(EmployeeBillHasTaxRate::class, ['employee_bill_id' => 'employee_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxRates()
    {
        return $this->hasMany(TaxRate::class, ['tax_rate_id' => 'tax_rate_id'])->viaTable('employee_bill_has_tax_rate', ['employee_bill_id' => 'employee_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBillItems()
    {
        return $this->hasMany(EmployeeBillItem::class, ['employee_bill_id' => 'employee_bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::class, ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }

    /**
     * Valida la fecha minima de creación del comprobante.
     */
    public function validateMinimunDate($attribute, $params) {
        $days = Config::getValue('limit_days_to_create_provider_bill') ;
        $min_date = (new \DateTime('now'))->modify("-$days days");

        if((new \DateTime($this->date ))->getTimestamp() < $min_date->getTimestamp()) {
            $this->addError($attribute, Yii::t('app', 'Date must be greater than {date}' , ['date' => $min_date->format('d-m-Y')]));
        }
    }

    /**
     * @brief Sets TaxRateTaxRates relation on helper variable and handles events insert and update
     */
    public function setEmployeePayments($employeePayments){

        if(empty($employeePayments)){
            $employeePayments = [];
        }

        $this->_employeePayments = $employeePayments;

        $saveEmployeePayments = function($event){
            $this->unlinkAll('employeePayments', true);

            foreach ($this->_employeePayments as $id) {
                $this->link('employeePayments', EmployeePayment::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveEmployeePayments);
        $this->on(self::EVENT_AFTER_UPDATE, $saveEmployeePayments);
    }

    /**
     * @brief Sets TaxRates relation on helper variable and handles events insert and update
     */
    public function setTaxRates($taxRates){

        if(empty($taxRates)){
            $taxRates = [];
        }

        $this->_taxRates = $taxRates;

        $saveTaxRates = function($event){
            $this->unlinkAll('taxRates', true);

            foreach ($this->_taxRates as $id) {
                $this->link('taxRates', TaxRate::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveTaxRates);
        $this->on(self::EVENT_AFTER_UPDATE, $saveTaxRates);
    }


    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            $this->formatDatesBeforeSave();
            $this->joinFieldsNumbers();
            /*if($this->type == 'A'){
                $this->total = $this->taxes + $this->net;
            }*/
            return true;

        }else{
            return false;
        }
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->formatDatesAfterFind();
        $this->formatNumber();
        parent::afterFind();
    }

    public function formatNumber(){
        if($this->number){
            if(strpos($this->number, '-')){
                $arrayNumbers = explode('-',$this->number);
                $this->number1 = $arrayNumbers[0];
                $this->number2 = $arrayNumbers[1];
            } else {
                $this->number2 = abs($this->number);
            }
        }
    }

    public function joinfieldsNumbers(){
        $this->number = $this->number1 . '-' .$this->number2;
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
        if ($this->date instanceof \DateTime) {
            $this->date = $this->date->format('d-m-Y');
        }

        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

    /**
     * @return bool
     * Indica si el modelo puede eliminarse
     */
    public function getDeletable()
    {
        if (count($this->employeeBillHasEmployeePayments) != 0) {
            return false;
        };

        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        if($this->status == EmployeeBill::STATUS_CLOSED) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * Indica si el modelo puede actualizarse
     */
    public function getUpdatable()
    {
        if(!AccountMovementRelationManager::isDeletable($this)) {
            return false;
        }

        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: BillType, Employee, EmployeeBillHasEmployeePayments, EmployeePayments, EmployeeBillHasTaxRates, TaxRateTaxRates.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('employeePayments', true);
        $this->unlinkAll('employeeBillHasTaxRates', true);
        $this->unlinkAll('employeeBillItems', true);
        AccountMovementRelationManager::delete($this);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                // Si esta cerrado, se genero el movimiento contable, por lo que tengo que revertirlo
                if($this->status == EmployeeBill::STATUS_CLOSED){
                    $amr = AccountMovementRelationManager::find($this);
                    $countMov = CountableMovement::getInstance();

                    if( $amr && !($account_movement_id = $countMov->revertMovement($amr->account_movement_id) ) ) {
                        Yii::$app->session->addFlash('error', Yii::t('accounting', 'The movement could not be created.'));
                        foreach($countMov->getErrors() as $error) {
                            Yii::$app->session->addFlash('error', $error);
                        }
                        return false;
                    }
                }
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Agrega un impuesto al comprobante.
     *
     * @param $tax_has
     * @return EmployeeBillHasTaxRate|null|static
     */
    public function addTax($tax_has)
    {
        if($tax_has['employee_bill_id']){
            $tax = EmployeeBillHasTaxRate::findOne([
                'employee_bill_id'=> $tax_has['employee_bill_id'],
                'tax_rate_id'=> $tax_has['tax_rate_id']]);
        }
        if(empty($tax)) {
            $tax = new EmployeeBillHasTaxRate();
            $tax->setAttributes($tax_has);
            $this->link('employeeBillHasTaxRates', $tax);
        } else {
            $tax->save();
        }

        return $tax;
    }

    /**
     * Agrega un Item al comprobante
     *
     * @param $item
     * @return EmployeeBillItem|null|static
     */
    public function addItem($item_bill)
    {
        if($item_bill['employee_bill_id']){
            $item = EmployeeBillItem::findOne([
                'employee_bill_id'=> $item_bill['employee_bill_id'],
                'account_id'=> $item_bill['account_id'],
                'amount'=> $item_bill['amount'],
                'description'=> $item_bill['description'],
            ]);
        }
        if(empty($item)) {
            $item = new EmployeeBillItem();
            $item->setAttributes($item_bill);
            $this->link('employeeBillItems', $item);
        }

        $this->calculateTotal();
        $item->save();

        return $item;
    }

    /**
     * Calcula el importe total del comprobante, incluyendo impuestos.
     * Incluye los impuestos con porcentaje cero en el neto del comprobante.
     *
     * @return float
     */
    public function calculateTotal()
    {
        $this->calculateItems();
        $this->calculateTaxes();
        $payment = $this->calculatePayment();
        $this->total = $this->net + $this->taxes;

        $this->balance = 0;
        if ($this->billType) {
            if ($this->billType->multiplier>0) {
                $this->balance = $this->total - $payment;
            }
        }


        $this->updateAttributes(['net', 'total', 'taxes', 'balance']);

        return  $this->total;
    }

    /**
     * Calcula los impuestos.
     *
     * @return int|mixed
     */
    public function calculateTaxes()
    {
        $taxes = 0;
        $all_taxes = $this->getEmployeeBillHasTaxRates()->innerJoin('tax_rate', 'tax_rate.tax_rate_id = employee_bill_has_tax_rate.tax_rate_id')->all();
        foreach($all_taxes as $tax) {
            $taxes += $tax->amount;
        }
        $this->taxes = $taxes;
        return $taxes;
    }

    public function calculatePayment()
    {
        $payment_amount = 0;
        foreach( $this->getEmployeeBillHasEmployeePayments()->all()  as $payment) {
            $payment_amount += $payment->amount;
        }

        return $payment_amount;
    }

    /**
     * Calcula el importe total de los items..
     *
     * @return float
     */
    public function calculateItems()
    {
        $totalItems = 0;
        foreach ($this->getEmployeeBillItems()->all() as $item) {
            $totalItems += $item->amount;
        }
        $this->net = $totalItems;
        return $totalItems;
    }

    public function getConfig()
    {
        // Traigo los impuestos y sus porcentajes, para distirlos uno de otro.
        $query = TaxRate::find();
        $query->select(['tax_rate.tax_rate_id', "concat(tax.name, ' ', (tax_rate.pct * 100), '%' ) as name", 'tax_rate.tax_id'])
            ->leftJoin('tax', 'tax_rate.tax_id = tax.tax_id');
        $taxes = ArrayHelper::map($query->asArray()->all(), 'tax_rate_id', 'name');

        foreach ($this->getEmployeeBillItems()->all() as $item) {
            if($item->account) {
                $taxes['items'][$item->account_id] = 'Item ' . $item->account_id;
            }
        }

        $taxes['totalItems'] = 'Total Items';
        $taxes['total'] = 'Total';
        $taxes['rest'] = 'Resto';
        return $taxes;
    }

    public function getAmounts()
    {
        $taxes = [];
        $rest = 0;
        foreach ($this->getEmployeeBillHasTaxRates()->all() as $tax ) {
            $taxes[$tax->taxRate->tax_rate_id] = [
                'tax_id' => $tax->taxRate->tax_rate_id,
                'amount' => $tax->amount,
                'base'   => 0
            ];
            $rest += $tax->amount;
        }

        $totalItems = 0;
        $descuento = 0;
        $iItems = 0;
        foreach ($this->getEmployeeBillItems()->all() as $item) {
            if($item->account) {
                $taxes['items'][$item->account_id] = $item->amount;
            }
            if($item->amount<0) {
                $descuento += abs($item->amount);
            }

            $totalItems += $item->amount;
            $iItems++;
        }

        if($descuento) {
            $porDesc = $descuento / $totalItems;
            if(array_key_exists('items', $taxes)) {
                foreach($taxes['items'] as $key=>$value) {
                    $taxes['items'][$key] = $taxes['items'][$key] - $porDesc;
                }
            } else {

            }
        }

        return $taxes + [
            'total' => $this->total,
            'rest'  => $this->total - $rest ,
            'totalItems' => $totalItems
        ];
    }

    /**
     * @return bool
     * Cierra el comprobante del proveedor.
     */
    public function close()
    {
        if ($this->status == EmployeeBill::STATUS_DRAFT) {
            // Calculo el total del comprobante.
            $this->calculateTotal();
            $this->status = EmployeeBill::STATUS_CLOSED;

            return $this->save();
        }
    }

    /**
     * Devuelve el importe restante de la factura. Si aun no hay ningun pago,
     * devolvera el importe total de la factura.
     * @return real
     */
    public function getDebt(){

        $payedAmount = $this->getPayedAmount(true);

        $total = $this->total;

        if(abs($total - $payedAmount) > $total * Yii::$app->params['payment_tolerance']){
            return $total - $payedAmount;
        }else{
            return 0.0;
        }

    }

    /**
     * Devuelve el monto que ha sido pagado del comprobante
     * @return type
     */
    public function getPayedAmount($includeDraft=false)
    {

        $payedAmount = 0.0;
        foreach($this->employeePayments as $payment){
            if ( ($includeDraft && $payment->status=='draft') || ($payment->status!='draft')) {
                $payedAmount += $payment->amount;
            }
        }

        return $payedAmount;

    }
}
