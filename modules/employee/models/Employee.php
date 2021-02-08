<?php

namespace app\modules\employee\models;

use app\components\helpers\CuitValidator;
use app\modules\accounting\models\Account;
use app\modules\sale\models\Address;
use app\modules\sale\models\BillType;
use app\modules\sale\models\Company;
use app\modules\sale\models\DocumentType;
use app\modules\sale\models\TaxCondition;
use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property integer $employee_id
 * @property string $name
 * @property string $lastname
 * @property string $document_number
 * @property integer $document_type_id
 * @property integer $address_id
 * @property string $phone
 * @property string $email
 * @property integer $account_id
 * @property integer $tax_condition_id
 * @property integer $company_id
 * @property string $birthday
 * @property integer $employee_category_id
 * @property integer $init_date
 * @property integer $finish_date
 * @property string $observations
 *
 * @property TaxCondition $taxCondition
 * @property DocumentType $documentType
 * @property Company $company
 * @property Account $account
 * @property EmployeeBill[] $employeeBills
 * @property EmployeePayment[] $employeePayments
 * @property EmployeeCategory $employeeCategory
 */
class Employee extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['name','lastname', 'tax_condition_id', 'document_type_id', 'document_number', 'address_id', 'company_id', 'employee_category_id'], 'required'],
            [['account_id', 'init_date', 'finish_date', 'observations'], 'safe'],
            [['birthday', 'observations'], 'string'],
            [['name', 'lastname'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 45],
            [['account_id', 'address_id', 'company_id'], 'number'],
            ['document_number', 'compareDocument']

        ];

        if (Yii::$app->getModule('accounting')) {
            $rules[] = [['account_id'], 'number'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employee_id' => Yii::t('app', 'Employee'),
            'name' => Yii::t('app', 'Name'),
            'lastname' => Yii::t('app', 'Lastname'),
            'document_number' => Yii::t('app', 'Document Number'),
            'address_id' => Yii::t('app', 'Address'),
            'phone' => Yii::t('app', 'Phone'),
            'account_id' => Yii::t('accounting', 'Account'),
            'tax_condition_id' => Yii::t('app', 'Tax Condition'),
            'birthday' => Yii::t('app', 'Birthdate'),
            'fullName' => Yii::t('app', 'Name'),
            'employee_category_id' => Yii::t('app', 'Employee Category'),
            'init_date' => Yii::t('app', 'Start Work Date'),
            'finish_date' => Yii::t('app', 'Finish Work Date'),
            'observations' => Yii::t('app', 'Observations'),
            'document_type_id' => Yii::t('app', 'Document Type')

        ];
    }

    public function compareDocument()
    {
        if($this->document_type_id != 1) {

            if (strlen($this->document_number) < 7 ||  strlen($this->document_number) > 8) {
                $this->addError('document_number', Yii::t('app','The document number must be beetwen 7 and 8 characters'));
            }

            $array_document = str_split($this->document_number);
            $array_caracters = array_count_values($array_document);

            Yii::info(count($array_caracters));

            if(count($array_caracters) == 1 && (array_key_exists('0', $array_caracters) || array_key_exists('9', $array_caracters))) {
                $this->addError('document_number', Yii::t('app','Invalid document number'));
            }

            if ($array_document[0] == '0') {
                $this->addError('document_number', Yii::t('app','Document number can`t start with 0'));
            }

        } else {
            $validator = new CuitValidator();
            $validator->validateAttribute($this, 'document_number');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeBills()
    {
        return $this->hasMany(EmployeeBill::class, ['employee_id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeePayments()
    {
        return $this->hasMany(EmployeePayment::class, ['employee_id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCondition()
    {
        return $this->hasOne(TaxCondition::class, ['tax_condition_id' => 'tax_condition_id']);
    }

    public function getDocumentType() {
        return $this->hasOne(DocumentType::class, ['document_Type_id' => 'document_type_id']);
    }

    public function getCompany() {
        return $this->hasOne(Company::class, ['company_id' => 'company_id']);
    }

    public function getAddress() {
        return $this->hasOne(Address::class, ['address_id' => 'address_id']);
    }

    public function getFullName() {
        return $this->name . ' '. $this->lastname;
    }

    public function getDeletable(){
    
        if($this->getEmployeeBills()->exists()){
            return false;
        }
        if($this->getEmployeePayments()->exists()){
            return false;
        }
        return true;
    }


    /**
     * @return array
     * Devuelve todos los posibles tipos de comprobantes.
     */
    public static function getAllBillTypes()
    {
        return [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C'
        ];
    }

    public function beforeSave($insert)
    {
        $this->formatDateBeforeSave();

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->formatDateAfterFind();
    }

    private function formatDateBeforeSave() {
        if ($this->birthday) {
            $this->birthday = Yii::$app->formatter->asDate($this->birthday, 'yyyy-MM-dd');
        }

        if($this->init_date) {
            $this->init_date = strtotime(Yii::$app->formatter->asDate($this->init_date, 'yyyy-MM-dd'));
        }

        if($this->finish_date) {
            $this->finish_date = strtotime(Yii::$app->formatter->asDate($this->finish_date, 'yyyy-MM-dd'));
        }
    }

    private function formatDateAfterFind() {
        if ($this->birthday) {
            $this->birthday = Yii::$app->formatter->asDate($this->birthday, 'dd-MM-yyyy');
        }

        if($this->init_date) {
            $this->init_date = Yii::$app->formatter->asDate($this->init_date, 'dd-MM-yyyy');
        }

        if($this->finish_date) {
            $this->finish_date = Yii::$app->formatter->asDate($this->finish_date, 'dd-MM-yyyy');
        }
    }

    /**
     * Devuelve el BillType del empleado de acuerdo al TaxCondition y a la empresa que pertenece
     */
    public function getBillType() {
        //Por defecto traigo factura B
        $billType = BillType::findOne(['name' => 'Factura B']);

        if ($this->taxCondition->name === 'IVA Inscripto') {
            // Si es Inscripto verifico que la empresa admita Factura A
            $ABillType = BillType::findOne(['Factura A']);
            if ($this->company->checkBillType($ABillType)){
                return $ABillType;
            }

            //Si no admite Factura A, verifico con Factura B
            if ($this->company->checkBillType($billType)){
                return $billType;
            }

            // Si hasta este punto no pude validar los billTypes anteriores, devuelvo el tipo por defecto de la empresa
            $billType = $this->company->defaultBillType;
        }else {
            //Verifico si admite Factura B
            if ($this->company->checkBillType($billType)) {
                return $billType;
            }

            // De no admitir Factura B devuelvo el tipo por defecto de la empresa
            $billType = $this->company->defaultBillType;
        }

        return $billType;
    }

    public function getEmployeeCategory() {
        return $this->hasOne(EmployeeCategory::class, ['employee_category_id' => 'employee_category_id']);
    }


}
