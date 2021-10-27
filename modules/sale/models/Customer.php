<?php

namespace app\modules\sale\models;

use Yii;
use DateTime;
use yii\db\Query;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\web\HttpException;
use Codeception\Util\Debug;
use yii\helpers\ArrayHelper;
use app\modules\config\models\Config;
use app\modules\ticket\models\Ticket;
use app\modules\westnet\models\Vendor;
use app\modules\checkout\models\Payment;
use app\modules\westnet\models\EmptyAds;
use app\components\helpers\CuitValidator;
use app\modules\sale\models\bills\Credit;
use app\components\companies\ActiveRecord;
use app\modules\accounting\models\Account;
use app\modules\westnet\models\Connection;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\westnet\models\NotifyPayment;
use yii\validators\RegularExpressionValidator;
use webvimark\modules\UserManagement\models\User;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\models\CustomerCompanyHistory;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\firstdata\models\FirstdataAutomaticDebit;
use app\modules\westnet\models\ConnectionForcedHistorial;
use app\modules\sale\modules\contract\models\ProgrammedPlanChange;
use app\modules\sale\components\CodeGenerator\CodeGeneratorFactory;
use app\modules\westnet\reports\models\CustomerUpdateRegister;
use app\modules\automaticdebit\models\AutomaticDebit;

/**
 * This is the model class for table "customer".
 *
 * @property integer $customer_id
 * @property string $name
 * @property string $lastname
 * @property string $document_number
 * @property integer $document_type_id
 * @property string $sex
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $status
 * @property integer $address_id
 * @property string $email2
 * @property string $phone2
 * @property string $phone3
 * @property integer $customer_reference_id
 * @property string $code
 * @property string $payment_code
 * @property integer $tax_condition_id 
 * @property string $publicity_shape
 * @property integer $sms_notification
 * @property integer $screen_notification
 * @property integer $email_notification
 * @property string $sms_fields_notifications
 * @property string $email_fields_notifications
 * @property integer $parent_company_id
 * @property integer $needs_bill
 * @property string $birthdate
 * @property string $observations
 * @property string $has_debit_automatic
 * @property string $has_direct_debit
 * @property integer $hash_customer_id
 * @property string $description
 *
 * @property Bill[] $bills
 * @property Profile[] $profiles
 * @property DocumentType[] $documentType
 * @property CustomerCategory[] $customerCategories
 * @property Account $account
 * @property CustomerClass[] $customerClass
 * @property Customer $customerReference
 * @property CustomerHasDiscount[] $customerHasDiscounts
 * @property TaxCondition $taxCondition
 * @property Company $parentCompany
 */
class Customer extends ActiveRecord {

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
    const STATUS_BLOCKED = 'blocked';

    //Email status
    const EMAIL_STATUS_ACTIVE = 'active';
    const EMAIL_STATUS_BOUNCED = 'bounced';
    const EMAIL_STATUS_INACTIVE = 'inactive';
    const EMAIL_STATUS_INVALID = 'invalid';

    protected static $companyRequired = false;
    
    private $_profiles = [];
    private $_customerClass;
    private $_customerCategory;
    //Para optimizar y simplificar conteo de comprobantes
    private $_billsCount;
    // Se usa para saber si eñ cliente cambia de empresa;
    private $old_company_id;
    
    public $_sms_fields_notifications;
    public $_email_fields_notifications;
    public $_notifications_way;

    //Propiedad que se usa para devolver errores descriptivos cuando una función puede dar false por diferentes motivos.
    public $detailed_error;

    // Indica al IVR que el cliente es moroso
    public $debtor = false;

    // Indica al IVR que el cliente es nuevo
    public $isNew= false;

    //Indica si los datos que ya existen en la base de datos fueron verificados con el cliente
    public $dataVerified;

    public $profileClasses;


    public $user_napear = null;

    public $customer;
    public $total_client;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'customer';
    }



    public function beforeValidate()
    {
        // La mascara del input rellena los digitos faltantes con _, antes de validar los eliminamos
        $this->document_number= trim($this->document_number, '_');

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = [
            [['name', 'lastname', 'phone2'],'required', 'on' => 'insert'],
            [['tax_condition_id', 'publicity_shape', 'document_number', 'has_debit_automatic'], 'required'],
            [['status'], 'in', 'range'=>['enabled','disabled','blocked']],
            [['name', 'lastname' ], 'string', 'max' => 150],
            [['document_number', 'email', 'email2'], 'string', 'max' => 45],
            [['document_type_id', 'address_id', 'needs_bill'], 'integer'],
            [['phone','phone2', 'phone3','phone4'], 'integer' /**,'on' => 'insert'**/,  'max' => 9999999999 , 'message' => Yii::t('app', 'Only numbers. You must input the area code without 0 and in cell phone number case without 15.')],
            //[['phone','phone2', 'phone3', 'phone4'], 'string', 'on' => 'update'],
            [['sex'], 'string', 'max' => 10],
            [['email', 'email2'], 'email'],
            [['account_id'], 'number'],
            [['company_id', 'parent_company_id', 'customer_reference_id', 'publicity_shape', 'phone','phone2', 'phone3',
                'screen_notification', 'sms_notification', 'email_notification', 'sms_fields_notifications',
                'email_fields_notifications', '_notifications_way', '_sms_fields_notifications', '_email_fields_notifications',
                'phone4', 'last_update', 'hourRanges', 'birthdate', 'observations', 'dataVerified','has_direct_debit', 'hash_customer_id', 'description'], 'safe'],
            [['code', 'payment_code'], 'unique'],
            //['document_number', CuitValidator::className()],
            ['document_number', 'compareDocument'],
            [['last_calculation_current_account_balance', 'current_account_balance', 'detailed_error', 'document_image', 'tax_image'], 'safe'],

            //Validacion de teléfonos
            ['phone', 'compare', 'compareAttribute' => 'phone2', 'operator' => '!=', 'message' => Yii::t('app','Phones cant be repeated')],
            ['phone', 'compare', 'compareAttribute' => 'phone3', 'operator' => '!=', 'message' => Yii::t('app','Phones cant be repeated')],
            ['phone', 'compare', 'compareAttribute' => 'phone4', 'operator' => '!=', 'message' => Yii::t('app','Phones cant be repeated')],

            ['phone2', 'compare', 'compareAttribute' => 'phone', 'operator' => '!=', 'message' => Yii::t('app','Phones cant be repeated')],
            ['phone2', 'compare', 'compareAttribute' => 'phone3', 'operator' => '!=', 'message' => Yii::t('app','Phones cant be repeated')],
            ['phone2', 'compare', 'compareAttribute' => 'phone4', 'operator' => '!=', 'message' => Yii::t('app','Phones cant be repeated')],

            ['phone3', 'compare', 'compareAttribute' => 'phone', 'operator' => '!=', 'message' => Yii::t('app', 'Phones cant be repeated')],
            ['phone3', 'compare', 'compareAttribute' => 'phone2', 'operator' => '!=', 'message' => Yii::t('app', 'Phones cant be repeated')],
            ['phone3', 'compare', 'compareAttribute' => 'phone4', 'operator' => '!=', 'message' => Yii::t('app', 'Phones cant be repeated')],

            ['phone4', 'compare', 'compareAttribute' => 'phone', 'operator' => '!=', 'message' => Yii::t('app', 'Phones cant be repeated')],
            ['phone4', 'compare', 'compareAttribute' => 'phone2', 'operator' => '!=', 'message' => Yii::t('app', 'Phones cant be repeated')],
            ['phone4', 'compare', 'compareAttribute' => 'phone3', 'operator' => '!=', 'message' => Yii::t('app', 'Phones cant be repeated')],
            //['birthdate', 'validateBirthdate']
        ];


        $this->validatePhones();

        if (Yii::$app->getModule('accounting')) {
            $rules[] = [['account_id'], 'number'];
            $rules[] = [['account'], 'safe'];
        }

        //Reglas opcionales (configuracion en params de app)
        if(Yii::$app->params['document_number_required']){
            $rules[] = [['document_type_id', 'document_number'], 'required', 'on' => 'insert'];
        } else {
            //$this->docNumberValidation();
        }
        
        if(Yii::$app->params['class_customer_required']){
            $rules []= [['customerClass'], 'required'];
            $rules []= [['customerClass'], 'safe'];
        }
        
        if(Yii::$app->params['category_customer_required']){
            $rules []= [['customerCategory'], 'required', 'on' => 'insert'];
            $rules []= [['customerCategory'], 'safe'];
        }
        
        //Reglas adicionales definidas para los perfiles
        $classes = self::getEnabledProfileClasses();
        foreach($classes as $class){
            $vr = $class->getValidationRules();
            
            foreach($vr as $r){
                $rules[] = $r;
            }
        }
        
        //Validacione de regex de tipo de documento
        $this->regexValitation();

        // SI SI, HARDCODEADO!! Hay que cambiar los modelos para poder parametrizarlo, o meter config y eso...
        //        if($this->document_type_id != 1) {
        //            $this->docNumberValidation();
        //            //$rules[] = ['document_number', 'compare', 'compareValue' => 999999, 'operator' => '>=', 'type' => 'number'];
        //        }

        return $rules;
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
     * Verifica si el documento ingresado pertenece o no a un cliente.
     * De pertenecer a un cliente, verifica que no tenga deuda
     */
    public function validateCustomer()
    {
        if ($this->document_number) {
            $this->compareDocument();
            if ($this->hasErrors()){
                return ['status' => 'invalid'];
            }
            $customer = Customer::findOne(['document_number' => $this->document_number]);

            if ($customer) {
                if ($customer->hasDebt()) {
                    return [
                        'status' => 'debt',
                        'debt' => Yii::$app->formatter->asCurrency(abs($customer->getDebt()))
                    ];
                }else {
                    return ['status' => 'no_debt'];
                }
            }

            return ['status' => 'new'];
        }

        return false;
    }

    /**
     * Valida la fecha de nacimiento:
     * -Si el customer es Iva inscripto no requiere fecha de nacimiento
     * -Si el customer es nuevo y no es iva inscrpto, requiere fecha de nacimiento
     * -Si el customer no es nuevo y no es iva inscripto, si ya tenia fecha de nacimiento no permite dejar el campo vacio,
     * si no tenia fecha de nacimiento y tampoco se setea al actualizar valida como correcto el campo
     *
     * @return boolean true si el campo fecha de nacimiento es válido
     */
    public function validateBirthdate()
    {
        if ($this->tax_condition_id == 1) {
            return true;
        }

        if ($this->isNewRecord && empty($this->birthdate)) {
            $this->addError('birthdate', Yii::t('app','Birthdate can`t be empty'));
            return false;
        } else {
            if (empty($this->birthdate) && !empty($this->oldAttributes['birthdate'])){
                $this->addError('birthdate', Yii::t('app','Birthdate can`t be empty'));
                return false;
            }

        }

        if (!empty($this->birthdate)) {
            $time = time();
            $birtdate_timestamp = strtotime(Yii::$app->formatter->asDate($this->birthdate, 'yyyy-MM-dd'));

            if (($time - $birtdate_timestamp) < ((86400 * 365) * 18)) {
                $this->addError('birthdate', Yii::t('app','The customer must be older than 18 years old'));
                return false;
            }
        }

        return true;
    }

    public function insertRules()
    {
        
    }
    
    public function updateRules()
    {
        $rules = [
            
            [['status'], 'in', 'range'=>['enabled','disabled','blocked']],
            [['name', 'lastname' ], 'string', 'max' => 150],
            [['document_number', 'email', 'phone', 'email2', 'phone2', 'phone3'], 'string', 'max' => 45],
            [['document_type_id', 'address_id'], 'integer'],
            [['sex'], 'string', 'max' => 10],
            [['email', 'email2'], 'email'],
            [['account_id'], 'number'],
            [['company_id', 'customer_reference_id'], 'safe'],
            [['code', 'payment_code'], 'unique'],
        ];
        
        if (Yii::$app->getModule('accounting')) {
            $rules[] =  [['account_id'], 'number'];
            $rules[] =  [['account'], 'safe'];
        }

                
        if(Yii::$app->params['class_customer_required']){
            $rules []= [['customerClass'], 'required'];
            $rules []= [['customerClass'], 'safe'];
        }

        if (Yii::$app->params['category_customer_required']) {
            $rules [] = [['customerCategory'], 'required'];
            $rules [] = [['customerCategory'], 'safe'];
        }

        //Reglas adicionales definidas para los perfiles
        $classes = self::getEnabledProfileClasses();
        foreach ($classes as $class) {
            $vr = $class->getValidationRules();

            foreach ($vr as $r) {
                $rules[] = $r;
            }
        }
        if($this->document_type_id != 1) {
            $this->docNumberValidation();
            //$rules[] = ['document_number', 'compare', 'compareValue' => 999999, 'operator' => '>=', 'type' => 'number'];
        }
        //Validacione de regex de tipo de documento
        $this->regexValitation();

        return $rules;
    }

    /**
     * Valida que el tipo de usuario seleccionado tenga el tipo de documento requerido
     */
    private function regexValitation() {
        //Validaciones relacionadas a documento y tipo de cliente
        $regexValidation = function() {
           
            //Validamos tipo de documento de acuedo a tipo de cliente
            if ($this->taxCondition && $this->taxCondition->documentType) {

                $requiredDocType = $this->taxCondition->documentType;
                $currentType = $this->documentType ? $this->documentType->document_type_id : null;
                $valid= false;
                foreach($requiredDocType as $type){
                    if ($type->document_type_id === $currentType) {
                        $valid=true;                   
                    }
                }
                
                if (!$valid) {
                     $this->addError('document_type_id', Yii::t('app'
                            , 'Customer type "{taxCondition}" requires "{documentType}" to be set.'
                            , ['taxCondition' => $this->taxCondition->name, 'documentType' => $this->taxCondition->getDocumentTypesLabels()]));
                }
            }

            //Validamos el documento de acuerdo a DocumentType.regex
            if (isset($this->documentType) && $this->documentType->regex) {
                $validator = new RegularExpressionValidator(['pattern' => $this->documentType->regex]);
                $validator->validateAttribute($this, 'document_number');
            }
            
        };
        $this->on(self::EVENT_AFTER_VALIDATE, $regexValidation);
    }

    /**
     * Si se elige tipo de documento, valida que se cargue el numero
     */
    private function docNumberValidation() {

        $docNumberValidation = function() {
            if($this->document_type_id != 1) {
                if($this->document_number <= 999999) {
                    $this->addError('document_number', Yii::t('app', '{attribute} needs be greater than 99999.'
                        , ['attribute' => $this->getAttributeLabel('document_number')]));
                }
            }
        };
        $this->on(self::EVENT_AFTER_VALIDATE, $docNumberValidation);
    }

    public function validatePhones()
    {
        $validation = function (){
            if ($this->phone) {
                $phone_array = str_split($this->phone);
                $phone_characters = array_count_values($phone_array);

                Yii::info($phone_characters);

                if (count($phone_characters) == 1) {
                    $this->addError('phone', Yii::t('app','Invalid Phone'));
                }
            }

            if ($this->phone2) {
                $phone_array = str_split($this->phone2);
                $phone_characters = array_count_values($phone_array);

                Yii::info($phone_characters);

                if (count($phone_characters) == 1) {
                    $this->addError('phone2', Yii::t('app','Invalid Phone'));
                }
            } elseif (isset($this->oldAttributes['phone2']) && !empty($this->oldAttributes['phone2'])) {
                $this->addError('phone2', Yii::t('app', 'Cell Phone 1 can`t be empty'));
            }

            if ($this->phone3) {
                $phone_array = str_split($this->phone3);
                $phone_characters = array_count_values($phone_array);

                Yii::info($phone_characters);

                if (count($phone_characters) == 1) {
                    $this->addError('phone3', Yii::t('app','Invalid Phone'));
                }
            }


            if ($this->phone4) {
                $phone_array = str_split($this->phone4);
                $phone_characters = array_count_values($phone_array);

                Yii::info($phone_characters);

                if (count($phone_characters) == 1) {
                    $this->addError('phone4', Yii::t('app','Invalid Phone'));
                }
            }
        };

        $this->on(self::EVENT_AFTER_VALIDATE, $validation);
    }


    public function behaviors()
    {
        return [
            'date_new' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date_new'],
                ],
                'value' => function(){
                    return (new \DateTime('now'))->format('Y-m-d');
                }
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $labels = [
            'customer_id' => Yii::t('app', 'Customer ID'),
            'parent_customer_id' => Yii::t('app', 'Empresa Padre'),
            'name' => Yii::t('app', 'Name'),
            'lastname' => Yii::t('app', 'Lastname'),
            'document_number' => Yii::t('app', 'Document Number'),
            'document_type_id' => Yii::t('app', 'Document Type'),
            'documentType' => Yii::t('app', 'Document Type'),
            'sex' => Yii::t('app', 'Sex'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
            'status' => Yii::t('app', 'Status'),
            'tax_condition_id' => Yii::t('app', 'Customer Type'),
            'taxCondition' => Yii::t('app', 'Customer Type'),
            'account_id' => Yii::t('accounting', 'Account'),
            'address_id' => Yii::t('app', 'Address'),
            'email2' => Yii::t('app', 'Secondary Email'),
            'phone2' => Yii::t('app', 'Second Phone'),
            'phone3' => Yii::t('app', 'Third Phone'),
            'code' => Yii::t('app', 'Code'),
            'customer_reference_id' => Yii::t('app', 'Referenced by'),
            'company_id' => Yii::t('app', 'Company'),
            'payment_code' => Yii::t('app', 'Payment Code'),
            'Company' => Yii::t('app', 'Company'),
            'publicity_shape' => Yii::t('app', 'Publicity Shape'),
            'sms_fields_notifications' => Yii::t('app', 'Sms Fields Notifications'),
            'email_fields_notifications' =>Yii::t('app', 'Email Fields Notifications'),
            '_sms_fields_notifications' => Yii::t('app', 'Sms Fields Notifications'),
            '_email_fields_notifications' =>Yii::t('app', 'Email Fields Notifications'),
            'screen_notification' => Yii::t('app', 'Screen Notification'),
            'sms_notification' => Yii::t('app', 'Sms Notification'),
            'email_notification' => Yii::t('app', 'Email Notification'),
            '_notifications_way' => Yii::t('app', 'Notifications Way'),
            'parent_company_id' => Yii::t('app', 'Parent Company'),
            'needs_bill' => Yii::t('app', 'Needs Bill'),
            'phone4' => Yii::t('app', 'Cellphone 4'),
            'last_update' => Yii::t('app', 'Last update'),
            'hourRanges' => Yii::t('app', 'Customer Hour range'),
            'document_image' => Yii::t('app', 'Document image'),
            'tax_image' => Yii::t('app', 'Tax image'),
            'birthdate' => Yii::t('app','Birthdate'),
            'observations' => Yii::t('app', 'Observations'),
            'dataVerified' => Yii::t('app', 'Data Verified'),
            'has_debit_automatic' => Yii::t('app', 'Require Automatic Debit'),
            'has_direct_debit' => Yii::t('app', 'Require Direct Debit'),
	        'hash_customer_id' => Yii::t('app', 'Hash Cliente ID'),
	        'current_account_balance' => Yii::t('app','Current Account Balance'),
            'description' => Yii::t('app','Description'),
        ];

        //Labels adicionales definidos para los profiles
        $profiles = $this->profileClasses;

        if ($profiles) {
            foreach ($profiles as $profile) {
                $labels['profile_' . $profile->profile_class_id] = $profile->name;
            }
        }

        return $labels;
    }

    /**
     * @return ActiveQuery
     */
    public function getBills() {
        return $this->hasMany(Bill::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHourRanges() {
        return $this->hasMany(HourRange::class, ['hour_range_id' => 'hour_range_id'])->viaTable('customer_has_hour_range', ['customer_id' => 'customer_id']);
    }

    /**
     * Solo utilizable con modulo checkout activo
     * @return ActiveQuery
     */
    public function getPayments() {
        return $this->hasMany(Payment::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDocumentType() {
        return $this->hasOne(DocumentType::className(), ['document_type_id' => 'document_type_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getTaxCondition() {
        return $this->hasOne(TaxCondition::className(), ['tax_condition_id' => 'tax_condition_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount() {
        return $this->hasOne(Account::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomerReference() {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_reference_id']);
    }

    public function getContracts() {
        return $this->hasMany(Contract::className(), ['customer_id' => 'customer_id']);
    }

    public function getUserApps() {
        return $this->hasMany(UserApp::class, ['user_app_id' => 'user_app_id'])->viaTable('user_app_has_customer', ['customer_id' => 'customer_id']);
    }

    public function getCustomerHasCustomerMessages()
    {
        return $this->hasMany(CustomerHasCustomerMessage::class, ['customer_id' => 'customer_id']);
    }

    public function getNotifyPayments()
    {
        return $this->hasMany(NotifyPayment::class, ['customer_id' => 'customer_id']);
    }

    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['customer_id' => 'customer_id']);
    }

    public function getAutomaticDebit()
    {
        return $this->hasOne(AutomaticDebit::class, ['customer_id' => 'customer_id']);
    }


    /**
     * Despues de guardar, guarda los profiles
     * @param boolean $insert
     * @return boolean
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        foreach ($this->_profiles as $class_id => $value) {
            $this->setProfile($class_id, $value);
        }

        /**
         * Creamos los CustomerLogs cuando el ambiente no es de testing.
         */
        if (!YII_ENV_TEST){
            if ($insert) {
                $log = new CustomerLog();
                
                $log->createInsertLog($this->customer_id, 'Customer', $this->customer_id, $this->user_napear);
            } else {
                if ($this->dataVerified) {
                    $this->updateAttributes(['last_update' => (new DateTime('now'))->format('Y-m-d')]);
                    CustomerUpdateRegister::createRegister($this->customer_id);
                    $log = new CustomerLog();
                    $log->createUpdateLog($this->customer_id, 'Verificación de Datos', '', '', 'Customer', $this->customer_id);
                }

                foreach ($changedAttributes as $attr => $oldValue) {
                    if ($this->$attr != $oldValue) {
//                        if($attr == 'document_number' || $attr == 'email' || $attr == 'email2' || $attr == 'phone'  || $attr == 'phone2' || $attr == 'phone3' || $attr == 'phone4' || $attr == 'hourRanges') {
                            $this->updateAttributes(['last_update' => (new \DateTime('now'))->format('Y-m-d')]);
//                        }

                        if ($attr === 'email') {
                            $this->updateAttributes(['email_status' => 'invalid']);
                        }

                        if ($attr === 'email2') {
                            $this->updateAttributes(['email2_status' => 'invalid']);
                        }

                        switch ($attr){
                            case 'address_id':
                                $oldAddress= Address::findOne(['address_id' => $oldValue]);
                                $log = new CustomerLog();
                                $log->createUpdateLog($this->customer_id, $this->attributeLabels()['Address'], $oldAddress->fullAddress, $this->address->fulAddress, 'Customer', $this->customer_id);
                                break;
                            case 'company_id':
                                $oldCompany= Company::findOne(['company_id' => $oldValue]);
                                $log = new CustomerLog();
                                // Inserto en Tabla: costumer_company_changed
                                $CompanyHist = new CustomerCompanyHistory();
                                $CompanyHist->old_company_id = $oldValue;
                                $CompanyHist->new_company_id = $this->company_id;
                                $CompanyHist->customer_id = $this->customer_id;
                                $CompanyHist->save();

                                $log->createUpdateLog($this->customer_id, $this->attributeLabels()['Company'], ($oldCompany ? $oldCompany->name : '' ), ($this->company ? $this->company->name: '' ), 'Customer', $this->customer_id);
                                break;
                            default:
                                $log = new CustomerLog();
                                $log->createUpdateLog($this->customer_id, $this->attributeLabels()[$attr], $oldValue, $this->$attr, 'Customer', $this->customer_id);
                                break;
                        }
                    }
                }
            }
        }



    }

    /**
     * @return ActiveQuery
     */
    public function getCustomerProfiles() {
        return $this->hasMany(Profile::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * Devuelve una lista con las clases de profile (ProfileClass) activas.
     * @return []
     */
    public static function getEnabledProfileClasses() {

        return ProfileClass::find()->where(['status' => 'enabled'])->orderBy(['order' => SORT_ASC])->all();
    }

    /**
     * Devuelve una lista con las clases de profile (ProfileClass) activas.
     * @return []
     */
    public static function getSearchableProfileClasses() {

        return ProfileClass::find()->where(['status' => 'enabled', 'searchable' => true])->all();
    }

    public function getCustomerHasDiscounts() {
        return $this->hasMany(CustomerHasDiscount::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * Retorna todos los descuentos Activos del cliente.
     *
     * @return Query
     */
    public function getActiveCustomerHasDiscounts($date = null) {

        if ($date) {
            $dateNow = $date->format('Y-m-d');
        } else {
            $dateNow = (new \DateTime('now'))->format('Y-m-d');
        }

        return $this->hasMany(CustomerHasDiscount::class, ['customer_id' => 'customer_id'])
            ->leftJoin('discount', 'discount.discount_id = customer_has_discount.discount_id')
            ->where(['and',
                ['<=', 'customer_has_discount.from_date', $dateNow],
                ['>=', 'customer_has_discount.to_date', $dateNow],
                ['discount.persistent' => 0],
            ])
            ->orWhere(['and',
                ['not', ['discount.persistent' => null]],
                ['customer_has_discount.to_date' => null],
            ])
            ->andWhere(['customer_has_discount.status' => CustomerHasDiscount::STATUS_ENABLED])
            ->andWhere(['discount.status' => Discount::STATUS_ENABLED]);
    }

    /**
     * Devuelve una lista de valores de cada profile indexados por clase (id de clase) de cada uno
     * para el customer actual.
     * @return type
     */
    public function getProfiles() {

        $all = self::getEnabledProfileClasses();

        $allValues = ArrayHelper::map($all, 'profile_class_id', 'default');

        $current = $this->customerProfiles;

        $currentValues = ArrayHelper::map($current, 'profile_class_id', 'value');

        return array_replace($allValues, $currentValues);
    }

    /**
     * Uso interno. Atributo $profiles definido mediante setter.
     * @param [] $profiles
     * @throws HttpException
     */
    public function setProfiles($profiles) {
        if (!is_array($profiles))
            throw new HttpException(500, 'Array expected');
        $this->_profiles = $profiles;
    }

    /**
     * 
     * @param type $class_id
     * @return null | app\modules\sale\models\Profile
     */
    public function getProfile($class_id) {
        $class = ProfileClass::find()->where(['profile_class_id' => $class_id])->one();

        //Si el profile soporta multiples valores, devolvemos un array con todos los valores
        if ($class->multiple == true) {
            $profiles = $this->getCustomerProfiles()->where(['profile_class_id' => $class_id])->all();
            $values = [];
            foreach ($profiles as $profile)
                $values[] = $profile->value;
            return $values;
            //Si el profile no soporta multiples valores, devolvemos solo el valor
        } else {
            $profile = $this->getCustomerProfiles()->where(['profile_class_id' => $class_id])->one();
            if (!empty($profile))
                return $profile->value;
            else
                return null;
        }
    }

    /**
     * 
     * @param int $class_id
     * @param string $value
     * @throws HttpException
     */
    public function setProfile($class_id, $value) {

        $class = ProfileClass::find()->where(['profile_class_id' => $class_id])->one();
        if (empty($class))
            throw new HttpException(500, 'Profile not found.');

        //Si es multiple, llamamos a setProfile por cada valor
        if ($class->multiple == true) {
            if (!is_array($value))
                throw new HttpException(500, 'Array expected.');

            foreach ($value as $v) {
                if (is_array($v))
                    $v = serialize($v);

                $this->setProfile($class_id, $v);
            }
        }else {

            if (!$class->multiple) {
                //Si el cliente ya tiene asociado un valor para el profile, lo recuperamos, caso contrario, creamos uno nuevo
                $profile = $this->getCustomerProfiles()->where(['customer_id' => $this->customer_id, 'profile_class_id' => $class_id])->one();
                if (empty($profile)) {
                    $profile = new Profile();
                }
                $profile->value = $value;
                $profile->profile_class_id = $class_id;
                $this->link('customerProfiles', $profile);
            }
        }
    }

    /**
     * Redefinimos getter, setter y isset magicos para soportar profiles
     * @param type $name
     */
    public function __get($name) {

        if ($name != 'profiles' && strpos($name, 'profile') !== false) {
            if (isset($this->_profiles[substr($name, 8)]))
                return $this->_profiles[substr($name, 8)];

            return $this->getProfile(substr($name, 8));
        }
        return parent::__get($name);
    }

    public function __set($name, $value) {

        if ($name != 'profiles' && strpos($name, 'profile') !== false) {
            $this->_profiles[substr($name, 8)] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function __isset($name) {

        if ($name != 'profiles' && strpos($name, 'profile') === false)
            if (ProfileClass::find()->where(['profile_class_id' => substr($name, 8)])->exists())
                return true;

        return parent::__isset($name);
    }

    /**
     * Antes de eliminar, debemos eliminar los profiles
     * @return boolean
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {

            $profiles = $this->customerProfiles;
            foreach ($profiles as $p)
                $p->delete();

            $classes = CustomerHasClass::findAll(['customer_id' => $this->customer_id]);
            foreach ($classes as $p)
                $p->delete();

            $categories = CustomerHasCategory::findAll(['customer_id' => $this->customer_id]);
            foreach ($categories as $p)
                $p->delete();

            $logs = CustomerLog::findAll(['customer_id' => $this->customer_id]);
            foreach ($logs as $p)
                $p->delete();

            $this->unlinkAll('hourRanges', $this);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Solo un default (habilitado)
     * @param type $insert
     */
    public function beforeSave($insert) {

        if($this->isNewRecord  && !$this->company_id) {
            self::$companyRequired = false;
        }

        if (!$this->validateBirthdate()) {
            return false;
        }


        if (parent::beforeSave($insert)) {

            $this->formatDateBeforeSave();

            if (in_array('screen', $this->_notifications_way)) {
                 $this->screen_notification= true;                      
            }else{
                $this->screen_notification= false;
            }
            
            if (in_array('sms', $this->_notifications_way)) {
                $this->sms_notification = true;
                if($this->_sms_fields_notifications !== ''){
                    $this->sms_fields_notifications= implode(',', $this->_sms_fields_notifications);
                }                   
            }else{
                $this->sms_notification= false;
                $this->sms_fields_notifications= NULL;
            }
            
            if (in_array('email', $this->_notifications_way)) {
                $this->email_notification = true;
                if($this->_email_fields_notifications !== ''){
                    $this->email_fields_notifications= implode(',', $this->_email_fields_notifications);
                }                   
            }else{
                $this->email_notification= false;
                $this->email_fields_notifications= NULL;
            }
            
                        
                        
            //Define codigo de cliente
            if ($this->isNewRecord || empty($this->code)) {
                $i = 1;
                do {
                    $this->code = self::getNewCode();
                                    
                    $i++;
                } while (!$this->validate(['code']) || $i <= 100);
            }
            
            if ($this->isNewRecord) {
                $this->phone2= (string)$this->phone2;
                $this->phone3= (string)$this->phone3;
                $this->last_update = (new \DateTime('now'))->format('Y-m-d');
            }
            $this->updatePaymentCode();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Devuelve el nombre completo del cliente
     * @param string $template define el template para construir el nombre completo
     * @return string
     */
    public function getFullName($template = '{lastname}, {name} {description}') {

            return str_replace(['{lastname}','{name}', '{description}'], [$this->lastname, $this->name, '('.$this->description.')'], $template);
        

       
    }

    /**
     * Si tiene Bills, PaymentReceipts o Payments relacionados, no es eliminable.
     * @return boolean
     */
    public function getDeletable() {

        if ($this->getBills()->exists()) {
            return false;
        }

        if (Yii::$app->getModule('checkout')) {
            if ($this->getPayments()->exists()) {
                return false;
            }
        }

        if (Contract::find()->where(['customer_id' => $this->customer_id])->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Permite verificar si el cliente tiene el tipo de documento $type
     * @param DocumentType $type
     * @return boolean
     */
    public function hasDocumentType(DocumentType $type) {

        if (!empty($this->document_number) && $this->document_type_id == $type->document_type_id) {
            return true;
        }

        return false;
    }

    /**
     * Devuelve tipo de factura por defecto de acuerdo al tipo de cliente.
     * @return BillType
     */
    public function getDefaultBillType() {

        $type = $this->taxCondition->getBillTypes()->one();

        return $type;
    }

    public function checkBillType($billType) {

        //return $this->taxCondition->getBillTypes()->where(['bill_type.bill_type_id' => $billType->bill_type_id])->exists();
        return BillType::find()
            ->innerJoin('tax_condition_has_bill_type tchbt', 'tchbt.bill_type_id = bill_type.bill_type_id')
            ->andWhere(['tchbt.tax_condition_id' => $this->tax_condition_id, 'tchbt.bill_type_id' => $billType->bill_type_id])
            ->exists();
    }

    /**
     * Devuelve la Clase Actual del Cliente (mayor date_updated)
     */
    public function getCustomerClass() {
        if ($this->_customerClass && is_object($this->_customerClass)) {
            return $this->_customerClass;
        } elseif ($this->_customerClass) {
            return CustomerClass::findOne($this->_customerClass);
        }

        return $this->getCustomerClasses()->limit(1);
    }

    public function setCustomerClass($class) {
        if (is_object($class)) {
            $class = $class->customer_class_id;
        }

        $this->_customerClass = $class;

        if (($this->customerClass && $this->customerClass->customer_class_id != $class) || $class) {
            if (!empty($class)) {
                $saveClasses = function($event) {
                    $customerHasClass = new CustomerHasClass();
                    $customerHasClass->customer_id = $this->customer_id;
                    $customerHasClass->customer_class_id = $this->_customerClass;
                    $customerHasClass->date_updated = time();
                    $customerHasClass->save();
                };
                $this->on(self::EVENT_AFTER_INSERT, $saveClasses);
                $this->on(self::EVENT_AFTER_UPDATE, $saveClasses);
            }
        }
    }

    public function getCustomerClasses() {
        $query = CustomerClass::find();
        $query->innerJoin('customer_class_has_customer', 'customer_class_has_customer.customer_class_id=customer_class.customer_class_id');
        $query->orderBy(['date_updated' => SORT_DESC]);
        $query->where('customer_class_has_customer.customer_id=:customer_id', ['customer_id' => $this->customer_id]);

        return $query;
    }

    public function getCustomerCategory() {
        if ($this->_customerCategory) {
            return CustomerCategory::findOne($this->_customerCategory);
        }

        return $this->getCustomerCategories()->limit(1);
    }

    public function setCustomerCategory($category) {
        $this->_customerCategory = $category;

        if (($this->customerCategory && $this->customerCategory->customer_category_id != $category) || $category) {
            if (!empty($category)) {
                $saveCategories = function($event) {
                    $customerHasCategory = new CustomerHasCategory();
                    $customerHasCategory->customer_id = $this->customer_id;
                    $customerHasCategory->customer_category_id = $this->_customerCategory;
                    $customerHasCategory->date_updated = time();
                    $customerHasCategory->save();
                };
                $this->on(self::EVENT_AFTER_INSERT, $saveCategories);
                $this->on(self::EVENT_AFTER_UPDATE, $saveCategories);
            }
        }
    }

    /**
     * @param $hour_ranges
     * Crea la relacion para guardar los horarios disponibles del cliente
     */
    public function setHourRanges($hour_ranges) {
        if($hour_ranges) {
            //Borro relaciones anteriores
            $this->unlinkAll('hourRanges', $this);
            //Creo las nuevas relaciones
            $setRange = function () use ($hour_ranges) {
                foreach ($hour_ranges as $hour_range) {
                    $customer_has_hour_range = new CustomerHasHourRange([
                        'customer_id' => $this->customer_id,
                        'hour_range_id' => $hour_range
                    ]);
                    $customer_has_hour_range->save();
                    }
            };

            $this->on(ActiveRecord::EVENT_AFTER_INSERT, $setRange);
            $this->on(ActiveRecord::EVENT_AFTER_UPDATE, $setRange);
        }
    }

    public function getCustomerCategories() {
        $query = CustomerCategory::find();
        $query->innerJoin('customer_category_has_customer', 'customer_category_has_customer.customer_category_id=customer_category.customer_category_id');
        $query->orderBy(['date_updated' => SORT_DESC]);
        $query->where('customer_category_has_customer.customer_id=:customer_id', ['customer_id' => $this->customer_id]);

        return $query;
    }

    public function setAddress($address) {
        //$this->link('address',$address);
        $this->address_id = $address->address_id;
        //$this->update();
        return true;
    }

    public function getAddress() {
        return $this->hasOne(Address::className(), ['address_id' => 'address_id']);
    }

    public function afterFind() {
        /* if(Yii::$app->params['class_customer_required'])
          $this->customerClass=$this->getCustomerClass();
          if(Yii::$app->params['category_customer_required'])
          $this->customerCategory=$this->getCustomerCategory();
         */

        $this->formatDateAfterFind();

        $this->old_company_id = $this->company_id;
        
        $sms_fields= explode(',', $this->sms_fields_notifications);
        $email_fields = explode(',', $this->email_fields_notifications);
        
        foreach ($sms_fields as $field){
            $this->_sms_fields_notifications[]= $field;
        }
        
        foreach ($email_fields as $field){
            $this->_email_fields_notifications[]= $field;
        }
        
        if ($this->screen_notification) {
            $this->_notifications_way[] = 'screen';
        }
        
        if ($this->sms_notification) {
            $this->_notifications_way[] = 'sms';
        }
        
        if ($this->email_notification) {
            $this->_notifications_way[] = 'email';
        }

        $this->profileClasses = self::getEnabledProfileClasses();

        parent::afterFind();
    }

    /**
     * Establece los atributos que deben ser utilizados al momento de convertir
     * el objeto a array
     * @return type
     */
    public function fields() {
        return [
                'customer_id' ,
                'name',
                'lastname',
                'document_number',
                'document_type_id',
                'documentType',
                'sex',
                'email',
                'phone',
                'phone4',
                'fullAddress' => function($model, $field) {
                    return (string) $model->address;
                },
                'status',
                'taxCondition',
                'code',
            ];
        
    }

    /**
     * Devuelve un array con la cantidad de comprobantes por tipo y estado
     * @return array
     */
    public function countBills($billType, $status = null) {
        //Ejecutamos la query solo una vez y almacenamos el resultado en _billCounts
        if (!isset($this->_billsCount)) {

            $query = Bill::find();

            $query->select('bill.bill_id, bill.bill_type_id, status, count(*) as count');
            $query->groupBy('bill.bill_type_id, status');

            //Solo se cuentan activos
            $query->where(['customer_id' => $this->customer_id, 'active' => true]);

            $counts = $query->asArray()->all();

            $types = [];
            foreach ($counts as $type) {

                $type_id = $type['bill_type_id'];
                $type_count = (int) $type['count'];

                if (!isset($types[$type_id])) {
                    $types[$type_id] = [
                        'all' => $type_count,
                        $type['status'] => $type_count
                    ];
                } else {
                    $types[$type_id]['all'] = $types[$type_id]['all'] + $type_count;
                    $types[$type_id][$type['status']] = $type_count;
                }
            }

            $this->_billsCount = $types;
        }

        //Todos los estados
        if ($status == null) {
            if (isset($this->_billsCount[$billType])) {
                return $this->_billsCount[$billType]['all'];
            }
        }

        //Por estado
        if (isset($this->_billsCount[$billType]) && isset($this->_billsCount[$billType][$status])) {
            return $this->_billsCount[$billType][$status];
        }

        //Nada:
        return 0;
    }

    public static function getStatusRange() {
        return [
            'enabled' => Yii::t('app', 'Enabled'),
            'disabled' => Yii::t('app', 'Disabled'),
            'blocked' => Yii::t('app', 'Blocked')
        ];
    }

    public function hasDebt() {

        $total = $this->getDebt();

        if (YII_ENV_TEST) {
            Debug::debug('Total :'. $total);
            Debug::debug('Tolerance :'. Yii::$app->params['account_tolerance'] );
        }


        if ($total < -(Yii::$app->params['account_tolerance'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getDebt()
    {
        $searchModel = new PaymentSearch();
        $searchModel->customer_id = $this->customer_id;

        $total = $searchModel->accountTotal();

        return $total;
    }
    
    /**
     * Verifica si el usuario puede actualizar este cliente.
     * @return boolean
     */
    public function canUpdate(){
        if (!User::hasRole('seller')) {
            if (User::hasPermission('actualizar-clientes') || User::hasRole('seller-office')) {
                return true;
            }else{
                return false;
            }
        }else{
            if (!User::hasRole('seller-office')) {
                $contracts = Contract::findAll(['customer_id' => $this->customer_id]);
                foreach ($contracts as $contract) {
                    if ($contract->status !== Contract::STATUS_DRAFT) {
                        return false;
                    }                  
                }
                
                return true;
                
            }elseif (User::hasPermission('actualizar-clientes')) {
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function canView(){
         if (!User::hasRole('seller')) {
            if (User::hasPermission('customer-index') || User::hasRole('seller-office')) {
                return true;
            }else{
                return false;
            }
        }else{
            if (!User::hasRole('seller-office')) {
                $can= false;
                $contracts = Contract::findAll(['customer_id' => $this->customer_id]);
                $vendor= Vendor::findOne(['user_id' => User::getCurrentUser()->id]);
                foreach ($contracts as $contract) {
                    if ($contract->vendor_id === $vendor->vendor_id) {
                        $can= true;
                    }                  
                }
                
                return $can;
                
            }elseif (User::hasPermission('customer-index')) {
                return true;
            }else{
                return false;
            }
        }
    }

    public static function getNewCode(){
        $maxCodeInCustomer= (int)(new Query())
                            ->from('(SELECT code from customer WHERE code < 9900000) c')
                            ->max('c.code');
        
        $maxCodeInEmptyAds= EmptyAds::maxCode();
        
        if ((int)$maxCodeInCustomer > (int)$maxCodeInEmptyAds) {
            return $maxCodeInCustomer + 1;
        }else{
            return $maxCodeInEmptyAds + 1 ;
        }
    }
    
    public function verifyCompany($node) {
        if ($node->company_id === $this->company_id) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Cambia la empresa del cliente. Pero para los casos en que el cliente ya tenga un contrato activo, devuelve true pero no cambia la empresa.
     * 
     * @param type $companyId
     * @return boolean
     */
    public function changeCompany($companyId){
        if(Contract::find()->where(['customer_id' => $this->customer_id, 'status' => Contract::STATUS_ACTIVE])->count() == 0){
            $this->company_id = $companyId;

            if ($this->save()) {
                return true;
            } else {
                return false;
            }
        }else{
            return true;
        }
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentCompany()
    {
        return $this->hasOne(\app\modules\sale\models\Company::className(), ['company_id' => 'parent_company_id']);
    }

    /**
     * Devuelve el id de la empresa
     * @return int
     */
    public function getParentCompanyId()
    {
        return $this->parent_company_id;
    }

    public function updatePaymentCode($force = false)
    {
        if($this->company_id) {
            $company = Company::findOne($this->company_id);

            /**
             * Si es un nuevo registro o el codigo de pago es null, genera el codigo de pago,
             * de lo contrario verifica si la empresa cambio, en caso afirmativo cambia el codigo de pago, reemplazando
             * los digitos correspondiente a la empresa, el resto del codigo de pago se mantiene
             * El penultimo digito, antes del de validacion se genera aleatoriamente.
             */
            // Si es nuevo o cambio de empresa, genero el codigo
            if (($this->isNewRecord || $this->payment_code == '' || $this->payment_code == '0') ||
                (((int)$this->company_id) != ((int)$this->old_company_id) ||
                    (strlen($this->payment_code) != 11 && strlen($this->payment_code) != 14 )) ||
                $this->payment_code < 0 || $force
            ) {
               $this->payment_code = $this->generatePaymentCode($company);
            }
        } else {
            $this->payment_code = null;//-($this->code ? $this->code : rand());
        }
        $this->updateAttributes(['payment_code']);
        return $this->payment_code;
    }

    /**
     * Genera el codigo de pago en base a la empresa recibida y el codigo del cliente, si no recibe codigo de cliente, toma el propio del objeto,
     * de lo contrario usa el codigo recibido (Esto facilita el testing)
     */
    public function generatePaymentCode($company, $code = null) {

        if ($code == null) {
            $code = $this->code;
        }
        
        $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');

        /**
         * El total del digitos del codigo de pago debe ser 14, por lo que la identificacion del cliente debe tener como maximo 8 digitos
         */
        $complete = '';
        if ($company->code != '9999') {
            $complete = str_pad($complete, (8 - strlen($code)), '0', STR_PAD_LEFT);
        }

        $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . $complete.
            $code ;

        return $generator->generate($code);    
    }


    /**
     * @return array
     * Devuelve las posibles formas de notificación
     */
    public static function getNotificationWays()
    {
        return [
            'screen' => Yii::t('app', 'Screen'),
            'sms' => 'SMS',
            'email' => Yii::t('app', 'Email')
        ];
    }

    /**
     * @return array
     * Devuelve las posibles formas de notificación por sms
     */
    public static function getSMSNotificationWays()
    {
        return [
            'phone' => Yii::t('app', 'Phone'),
            'phone2' => Yii::t('app', 'Second Phone'),
            'phone3' => Yii::t('app', 'Third Phone'),
            'phone4' => Yii::t('app', 'Cellphone 4')
        ];
    }

    /**
     * @return array
     * Devuelve las posibles formas de notificación por email
     */
    public static function getEmailNotificationWays()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'email2' => Yii::t('app', 'Email 2')
        ];
    }

    /**
     * @return bool
     * @throws \Exception
     * Indica si el cliente requiere una actualización de los datos.
     */
    public function getNeedsUpdate()
    {
        if(!$this->last_update) {
            return true;
        }

        $maximun_months_update = Config::getValue('require_update_customer_data');
        $date_last_update = new \DateTime($this->last_update);
        $date_now = new \DateTime('now');
        $month_difference = $date_last_update->diff($date_now)->m + ($date_last_update->diff($date_now)->y*12);

        Yii::trace($this->last_update);
        Yii::trace($maximun_months_update);
        Yii::trace($date_last_update->diff($date_now)->m);
        Yii::trace(($date_last_update->diff($date_now)->y*1));
        Yii::trace('diferencia '. $month_difference );

        if($month_difference >= $maximun_months_update) {
            return true;
        }

        return false;
    }

    /**
     * @param $customer_code
     * @param $category_id
     * @param $is_open
     * @return array
     * Indica si el cliente tiene un ticket de la categoría indicada
     */
    public static function hasCategoryTicket($customer_code, $category_id, $is_open)
    {
        $initMonth = (new DateTime())->modify('first day of this month');
        $lastMonth = (new DateTime())->modify('last day of this month');

        $customer = Customer::findOne(['code' => $customer_code]);

        $ticket = Ticket::find()
            ->leftJoin('status', 'status.status_id = ticket.status_id')
            ->where(['customer_id' => $customer->customer_id, 'category_id' => $category_id, 'status.is_open' => $is_open ? 1 : 0])
            ->andWhere(['>=', 'start_datetime', $initMonth->getTimestamp()])
            ->andWhere(['<', 'start_datetime', ($lastMonth->getTimestamp() + 86400)])
            ->one();

        return [
            'customer_code' => $customer_code,
            'has_ticket' => $ticket ? true : false,
            'ticket_status' => $ticket ? $ticket->status->name : ''
        ];
    }

    public static function verifyEmails($data, $field = "email", $type = 'elastic')
    {
        $row_index = 0;
        $results = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'bounced' => 0
        ];
        while (($row = fgetcsv($data)) !== false) {
            Yii::info(print_r($row, 1), 'data');
            if ($row_index > 0) {
                if ($type == 'elastic') {
                    $customers = Customer::find()->andWhere([$field => $row[0], 'status' => 'enabled'])->all();

                    foreach ($customers as $customer) {
                        $status_field = $field.'_status';
                        $customer->$status_field = strtolower($row[1]);
                        $customer->updateAttributes([$status_field]);

                        $results['total']++;
                        $results[strtolower($row[1])]++;

                    }
                } else {
                    $customers = Customer::find()->andWhere([$field => $row[0], 'status' => 'enabled'])->all();

                    foreach ($customers as $customer) {
                        $status_field = $field.'_status';
                        $customer->$status_field = 'inactive';
                        $customer->updateAttributes([$status_field]);

                        $results['total']++;
                        $results['inactive']++;
                    }
                }
            }

            $row_index++;
        }

        return $results;
    }

    /**
     * @return bool
     * @throws \Exception
     * Determina si el cliente tiene la app instalada.
     * En el caso de tener asignado mas de un UserApp asociado, si al menos uno lo tiene instalado devuelve true;
     * También verifica que la última actividad sea en el rango de fecha que se detemina con el parametro de configuración
     */
    public function hasMobileAppInstalled()
    {
        $uninstalled_period = Config::getValue('month-qty-to-declare-app-uninstalled');
        $date_min_last_activity = (new \DateTime('now'))->modify("-$uninstalled_period month")->getTimestamp();
        $has_mobile_app_installed = false;

        if($this->getUserApps()->exists()) {
            foreach ($this->userApps as $user_app) {
                if($user_app->activity){
                    if($user_app->activity->last_activity_datetime >= $date_min_last_activity) {
                        $has_mobile_app_installed = true;
                    }
                }
            }
        }

        return $has_mobile_app_installed;
    }

    /**
     * @return array|string|\yii\db\ActiveRecord|null
     * Devuelve el último uso de la aplicación
     */
    public function lastMobileAppUse($formated = false)
    {
        $last_use = '';

        if ($this->getUserApps()->exists()) {
            $user_app_ids = [];

            foreach ($this->userApps as $user_app) {
                array_push($user_app_ids, $user_app->user_app_id);
            }

            $activity = UserAppActivity::find()->where(['in', 'user_app_id', $user_app_ids])->orderBy(['last_activity_datetime' => SORT_DESC])->one();
            $last_use = $activity ? $activity->last_activity_datetime : '';
        }

        if ($formated && $last_use) {
            return (new \DateTime())->setTimestamp($last_use)->format('Y-m-d');
        }

        return $last_use;
    }

    public function getSMSCount() {

        $first_day = (new DateTime())->modify('first day of this month')->getTimestamp();
        $last_day = (new DateTime())->modify('last day of this month')->getTimestamp();

        return $this->getCustomerHasCustomerMessages()->andWhere(['>=', 'timestamp', $first_day])->andWhere(['<', 'timestamp', ($last_day + 86400)])->count();
    }

    /**
     * @return bool
     * Indica si se puede enviar mas SMS al cliente.
     */
    public function canSendSMSMessage()
    {
        if($this->SMSCount <= (int)Config::getValue('sms_per_customer')) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * Envía un mensaje SMS con los links de descarga de la aplicación móvil
     */
    public function sendMobileAppLinkSMSMessage()
    {
        $id_customer_message = Config::getValue('link-to-app-customer-message-id');
        $customer_message = CustomerMessage::find()->where(['customer_message_id' => $id_customer_message, 'status' => CustomerMessage::STATUS_ENABLED])->one();
        $is_developer_mode = Config::getValue('is_developer_mode');

        if($this->canSendSMSMessage() && $customer_message) {
            //Sólo hago el envío de los mensajes con los links de la app si no está en modo de desarrollo
            if(!$is_developer_mode) {
                $result = $customer_message->send($this);
                if (array_key_exists('status', $result)) {
                    return $result['status'] == 'success' ? true : false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * @return array
     * Devuelve los estados posibles para ser listados en un desplegable o similar.
     */
    public static function getStatusEmailForSelect()
    {
        return [
            Customer::EMAIL_STATUS_ACTIVE => Yii::t('app', Customer::EMAIL_STATUS_ACTIVE),
            Customer::EMAIL_STATUS_BOUNCED => Yii::t('app', Customer::EMAIL_STATUS_BOUNCED),
            Customer::EMAIL_STATUS_INACTIVE => Yii::t('app', Customer::EMAIL_STATUS_INACTIVE),
            Customer::EMAIL_STATUS_INVALID => Yii::t('app', Customer::EMAIL_STATUS_INVALID)
        ];
    }

    /**
     * @param $customer_id
     * Devuelve la cantidad de facturas que adeuda un cliente.
     */
    public static function getOwedBills($customer_id)
    {
        $customer_search = new CustomerSearch();
        $owed_bills = $customer_search->searchDebtBills($customer_id);
        if(!$owed_bills) {
            return 0;
        }
        return $owed_bills['debt_bills'];
    }

    /**
     * @return int
     * @throws \Exception
     * Devuelve la cantidad de extensiones de pago pedidas en el período
     * Si se indica que cuente los informes de pago(que tambien generan que la conexion sea forzada) se restará 1 al
     * total de conexiones forzadas siempre que sean mayor a 1.
     */
    public function getPaymentExtensionQtyRequest($from = null, $to = null, $count_notify_payments = true)
    {
        $payment_extension_qty = 0;

        if (empty($from)) {
            $from = (new \DateTime('first day of this month'));
        }

        if (empty($to)) {
            $to = (new \DateTime('last day of this month'));
        }

        foreach ($this->getContracts()->all() as $contract) {
            $connection = Connection::findOne(['contract_id' => $contract->contract_id]);

            if ($connection) {
                $extension_qty = ConnectionForcedHistorial::find()
                    ->andWhere(['connection_id' => $connection->connection_id])
                    ->andWhere(['>=', 'create_timestamp', $from->getTimestamp()])
                    ->andWhere(['<', 'create_timestamp', $to->getTimestamp() + 86400])
                    ->count();

                $payment_extension_qty += $extension_qty;
            }

            if($count_notify_payments){
                //Si se tienen en cuenta los informes de pago, se debe restar 1 a la cantidad total. Si no puede
                // realizar un informe de pago, es porque ya se ha realizado el correspondiente a este mes
                if(!$this->canNotifyPayment() && $payment_extension_qty > 0 && $payment_extension_qty != 1) {
                    $payment_extension_qty -= 1;
                }
            }
        }

        return $payment_extension_qty;
    }

    /**
     * @param $customer_id
     * @param null $period
     * @return bool
     * @throws \Exception
     * Indica si el cliente puede pedir una extension de pago.
     * Logica de negocio:
     *
     * Si el item de config. de vencimiento de los comprobantes + item de config. de extension de pago informada
     * es mayor al dia corriente, el cliente no puede solicitar una extensión de pago. Ej: 15 + 5 = 20
     * Si hoy es 16, puedo solicitar una extension de pago
     * Si hoy es 21 ya no puedo solicitarla.
     * IMPORTANTE: Se puede consultar un detalle de porqué devuelve false haciendo $this->detailed_error
     */
    public function canRequestPaymentExtension()
    {

        $lastForced = $this->getLastForced();
        $timeBetween = (int)Config::getValue('time_between_payment_extension');

        if ($lastForced && ($lastForced->create_timestamp > (time() - ($timeBetween * 60)))) {
            return false;
        }


        $max_date_can_request_payment_extension = $this->getMaxDateNoticePaymentExtension();
        $today = (new \DateTime('now'))->getTimestamp();

        //Verifico que la fecha de hoy no sea mayor a la fecha máxima en la cual se puede solicitar la extension de pago
        if($today > $max_date_can_request_payment_extension) {
            $this->detailed_error = Yii::t('app', "Today's date exceeds the maximun date");
            return false;
        }

        //Sólo si el cliente no debe mas de una factura
        if(Customer::getOwedBills($this->customer_id) > (int)Config::getValue('payment_extension_debt_bills')) {
            $this->detailed_error = Yii::t('app', 'The customer have debt bills');
            $this->debtor = true;
        }

        if(!Customer::hasFirstBillPayed($this->customer_id, false)) {
            $this->detailed_error = Yii::t('app', 'The customer doesnt have the first bill payed');
            $this->debtor = true;
        }

        //Verifico que el cliente no sea nuevo
        if($this->isNewCustomer()) {
            $this->detailed_error = Yii::t('app', 'The customer is new');
            $this->isNew = true;
        }

        //Si es deudor o es nuevo, recien salgo aca para que pase por las 3 validaciones
        if ($this->debtor || $this->isNew) {
            return false;
        }

        //Y si no ha solicitado el máximo de extensiones de pago permitidas.
        $maximun_payment_extension_qty = Config::getValue('payment_extension_qty_per_month');
        $payment_extension_qty = $this->getPaymentExtensionQtyRequest();

        if($payment_extension_qty > $maximun_payment_extension_qty) {
            $this->detailed_error = Yii::t('app', 'The customer exceed the maximun payment extension quantity per month');
            return false;
        }

        return true;
    }

    /**
     * Verifica que el cliente tenga comprobantes en estado borrador
     */
    public function hasDraftBills()
    {
        return $this->getBills()->where(['status' => Bill::STATUS_DRAFT])->exists();
    }

    /**
     * Verifica que el cliente tenga pagos en estado borrador
     */
    public function hasDraftPayments()
    {
        return $this->getPayments()->where(['status' => Payment::PAYMENT_DRAFT])->exists();
    }

    /**
     * Indica si el cliente puede informar de un pago.
     * Sólo puede informar de un nuevo pago si no ha hecho el informe de un pago este mes
     */
    public function canNotifyPayment($date = null, $date_to = null)
    {
        //TODO: Eliminar condicion cuando finalize desarrollo de IVR
        if ($this->code === 27237) {
            return true;
        }
        if($date === null) {
            $date = (new \DateTime('first day of this month'))->format('Y-m-d');
        }

        if($date_to === null) {
            $date_to = (new \DateTime('last day of this month'))->format('Y-m-d');
        }

        return !$this->getNotifyPayments()->where(['>','date', $date])->andWhere(['<', 'date', $date_to])->exists();
    }

    /**
     * Devuelve un timestamp con la fecha max en la que se informará al cliente sobre una extension de pago
     */
    public static function getMaxDateNoticePaymentExtension()
    {
        $expiration_bill_days_qty = Config::getValue('bill_default_expiration_days');
        $payment_extension_days_qty = Config::getValue('payment_extension_duration_days');

        $day_of_the_month = $expiration_bill_days_qty + $payment_extension_days_qty;

        return (new \DateTime('first day of this month'))->modify("+$day_of_the_month days")->getTimestamp();
    }

    /**
     * Devuelve un timestamp con la fecha max real de una extension de pago
     */
    public static function getMaxDateRealPaymentExtension()
    {
        $expiration_bill_days_qty = Config::getValue('bill_default_expiration_days');
        $payment_extension_days_qty = Config::getValue('payment_extension_real_duration_days');

        $day_of_the_month = $expiration_bill_days_qty + $payment_extension_days_qty;

        return (new \DateTime('first day of this month'))->modify("+$day_of_the_month days")->getTimestamp();
    }

    /**
     * Indica si el cliente tiene un contrato activo.
     */
    public function hasActiveContract()
    {
        return $this->getContracts()->where(['status' => Contract::STATUS_ACTIVE])->exists();
    }

    /**
     * Determina si el cliente es nuevo en base de la fecha de validez del contrato.
     */
    public function isNewCustomer()
    {
        if ($this->code === 27237) {
           return false;
        }

        $contracts = $this->contracts;
        foreach ($contracts as $contract) {
            if (empty($contract->from_date) || $contract->from_date === Yii::t('app', 'Undetermined time')){
                return true;
            }

            if (strtotime(Yii::$app->formatter->asDate($contract->from_date, 'yyyy-MM-dd')) > (strtotime(date('Y-m-d')) - (86400 * (int)Config::getValue('new_contracts_days')))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Indica si el cliente tiene la primer factura pagada.
     */
    public static function hasFirstBillPayed($customer_id, $verify_bills = true)
    {
        $bill = Bill::find()->where(['customer_id' => $customer_id, 'status' => 'closed'])->orderBy(['bill_id' => SORT_ASC])->one();

        if(!$bill) {
            return false;
        }

        $total_payed = (new Query())
            ->select('SUM(amount) as total')
            ->from('payment')
            ->where(['customer_id' => $customer_id, 'status' => 'closed'])
            ->one();

        if($total_payed['total'] >= $bill->total) {
            return true;
        }

        return false;
    }

    /**
     * Indica si el cliente tiene un cambio de plan programdado.
     */
    public function hasPendingPlanChange() {
        $contracts = Contract::find()->andWhere(['customer_id' => $this->customer_id, 'status' => Contract::STATUS_ACTIVE])->all();
        $ids = array_map(function($contract) { return $contract->contract_id;}, $contracts);

        return ProgrammedPlanChange::find()->andWhere(['contract_id' => $ids, 'applied' => false])->exists();
    }

    /**
     * Devuelve el cambio de velocidad programado
     */
    public function getPendingPlanChange()
    {
        return ProgrammedPlanChange::find()
            ->innerJoin('contract c', 'c.contract_id=programmed_plan_change.contract_id')
            ->andWhere(['c.customer_id' => $this->customer_id, 'c.status' => Contract::STATUS_ACTIVE, 'applied' => false])
            ->orderBy(['programmed_plan_change.date' => SORT_DESC])
            ->one();
    }

    /**
     * Determina si el plan del contrato del cliente es un plan de fibra o no
     * Si el cliente no tiene contrato devuelve false.
     */
    public function hasFibraPlan()
    {
        $contract = $this->getContracts()->andWhere(['status' => Contract::STATUS_ACTIVE])->one();

        //Si no tengo activos busco cualquiera
        if(!$contract) {
            $contract = $this->getContracts()->orderBy(['contract_id' => SORT_DESC])->one();
        }

        //Verifico que tenga un contrato
        if(!$contract) {
            return false;
        }

        return $contract->hasFibraPlan();
    }

    /**
     * Devuelve el path de la imagen del documento
     */
    public function getDocumentImageWebPath()
    {
        return (!$this->document_image ? null : 'uploads/document_images/' . basename($this->document_image));
    }

    /**
     * Devuelve el path de la imagen del impuesto cargado
     */
    public function getTaxImageWebPath()
    {
        return (!$this->tax_image ? null : 'uploads/tax_images/' . basename($this->tax_image));
    }



    /**
     * Crea una nota de crédito por el total de la deuda del cliente, para darlo de baja
     * @return bool
     */
    public function createCreditForDebt()
    {
        $paymentSearch = new PaymentSearch();
        $paymentSearch->customer_id = $this->customer_id;

        $debt = round((float)$paymentSearch->accountTotal(), 2);

        Yii::info($debt, 'Deuda');

        if ($debt < 0) {
            $amount = abs($debt);
            //Verifico que el monto de deuda sea mayor a un peso para no tener inconvenientes al momento de calcular el IVA y presentar el comprobante
            if ($amount > 1) {
                if ($this->taxCondition->name === 'IVA Inscripto') {
                    $lastBillType = $this->getLastBillType();
                    if ($lastBillType && $lastBillType->name === 'Factura A') {
                        $billType = BillType::findOne(['name' => 'Nota Crédito A']);
                    } else {
                        $billType = BillType::findOne(['name' => 'Nota Crédito B']);
                    }
                } else {
                    if ($this->company_id != Config::getValue('ecopago_batch_closure_company_id')) {
                        $billType = BillType::findOne(['name' => 'Nota Crédito B']);
                    } else {
                        $billType = BillType::findOne(['name' => 'Descuento']);
                    }
                }

                $unit_final_price = $amount;
                $unit_net_price = (($amount * 100) / ((0.21 * 100) + 100));

                if (empty($billType)) {
                    return false;
                }

                if (!class_exists($billType->class)) {
                    return false;
                }

                $point_of_sale = $this->company->getPointsOfSale()->andWhere(['default' => 1])->one();

                if (empty($point_of_sale)) {
                    $point_of_sale = $this->company->getPointsOfSale()->one();
                    if (empty($point_of_sale)) {
                        Yii::$app->session->addFlash('error', 'Can`t found a point of sale for customer company');
                        return false;
                    }
                }

                $bill = Yii::createObject($billType->class);
                $bill->bill_type_id = $billType->bill_type_id;
                $bill->date = date('d-m-Y');
                $bill->status = Bill::STATUS_DRAFT;
                $bill->point_of_sale_id = $point_of_sale->point_of_sale_id;
                $bill->class = $billType->class;
                $bill->customer_id = $this->customer_id;
                $bill->company_id = $this->company_id;
                $bill->save();

                Yii::info($bill->getErrors(), 'Nota');
                Yii::info($debt, 'Deuda');

                $detail = $bill->addDetail([
                    'product_id' => Config::getValue('baja_product_id'),
                    'qty' => 1,
                    'unit_id' => Config::getValue('default_unit_id'),
                    'unit_net_price' => $unit_net_price,
                    'unit_final_price' => $unit_final_price,
                    'line_subtotal' => $unit_net_price,
                    'line_total' => $unit_final_price,
                    'concept' => 'Cancelación por baja(Automático)'
                ]);
                Yii::info($detail, 'Deuda');


                if ($detail == false) {
                    return false;
                }

                $detail->save();

                return $bill->close();
            }
        }

        return true;

    }

    public function getLastBillType()
    {
        $bill = Bill::find()
            ->innerJoin('bill_type bt', 'bt.bill_type_id = bill.bill_type_id')
            ->andWhere([
                'bill.customer_id' => $this->customer_id,
                'bt.multiplier' => '1'
            ])
            ->orderBy(['timestamp' => SORT_DESC])
            ->one();

        if ($bill) {
            return $bill->billType;
        }

        return null;

    }

    private function formatDateBeforeSave() {
        if ($this->birthdate) {
            $this->birthdate = Yii::$app->formatter->asDate($this->birthdate, 'yyyy-MM-dd');
        }
    }

    private function formatDateAfterFind() {
        if ($this->birthdate) {
            $this->birthdate = Yii::$app->formatter->asDate($this->birthdate, 'dd-MM-yyyy');
        }
    }

    /**
     * Devuelve un array con los canales de publicidad para ser listados en un selector
     */
    public static function getPublicityShapesForSelect()
    {
        return PublicityShape::getPublicityShapeForSelect();
    }

    /**
     * Devuelve el ultimo forzado de conexión del cliente
     */
    public function getLastForced()
    {
        $lastForced = ConnectionForcedHistorial::find()
            ->innerJoin('connection c', 'c.connection_id=connection_forced_historial.connection_id')
            ->innerJoin('contract con', 'con.contract_id=c.contract_id')
            ->andWhere(['con.customer_id' => $this->customer_id])
            ->orderBy(['connection_forced_historial.create_timestamp' => SORT_DESC])
            ->one();

        return $lastForced;
    }

    /**
     * Devuelve el ultimo comprobante cerrado, o false en caso de no encontrar ninguno.
     */
    public function getLastClosedBill()
    {
        $bill = $this->getBills()
            ->leftJoin('bill_type bt', 'bill.bill_type_id = bt.bill_type_id')
            ->andWhere(new Expression('bt.multiplier > 0'))
            ->andWhere(['status' => Bill::STATUS_CLOSED])
            ->orderBy(['date' => SORT_DESC])
            ->one();

        return $bill ? $bill : false;
    }

    public function inactiveFirstdataDebit() 
    {
        $debit = FirstdataAutomaticDebit::findOne(['customer_id' => $this->customer_id]);

        if ($debit && $debit->status === 'enabled'){
            $debit->status = 'disabled';
            $debit->save();
            Yii::trace($debit->getErrors());
        }
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
    }

    public static function findContractsActiveByCustomerId($customer_id){
        return self::find()->where(['status' => 'active'])->all();
    }

    /**
    * @Return total current_account_balance
    */
    public static function getTotalDebtorsCurrency(){
        return Yii::$app->db->createCommand("SELECT SUM(current_account_balance) AS total_debtors FROM customer cu
            INNER JOIN contract co ON co.customer_id = cu.customer_id
            WHERE co.status IN ('active', 'low-process') ")
            ->queryOne();
    }
}
