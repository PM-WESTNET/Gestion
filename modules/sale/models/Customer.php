<?php

namespace app\modules\sale\models;

use app\components\companies\ActiveRecord;
use app\components\helpers\CuitValidator;
use app\modules\accounting\models\Account;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\search\PaymentSearch;
use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\UserApp;
use app\modules\mobileapp\v1\models\UserAppActivity;
use app\modules\sale\components\CodeGenerator\CodeGeneratorFactory;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\ConnectionForcedHistorial;
use app\modules\westnet\models\NotifyPayment;
use app\modules\westnet\models\Vendor;
use app\modules\westnet\models\EmptyAds;
use Codeception\Util\Debug;
use DateTime;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\validators\RegularExpressionValidator;
use yii\web\HttpException;
use app\modules\ticket\models\Ticket;

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

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = [
            [['name', 'lastname'],'required', 'on' => 'insert'],
            [['tax_condition_id', 'publicity_shape', 'document_number'], 'required'],
            [['status'], 'in', 'range'=>['enabled','disabled','blocked']],
            [['name', 'lastname' ], 'string', 'max' => 150],
            [['document_number', 'email', 'email2'], 'string', 'max' => 45],
            [['document_type_id', 'address_id', 'needs_bill'], 'integer'],
            [['phone','phone2', 'phone3'], 'integer', 'on' => 'insert', 'message' => Yii::t('app', 'Only numbers. You must input the area code without 0 and in cell phone number case without 15.')],
            [['phone','phone2', 'phone3', 'phone4'], 'string', 'on' => 'update'],
            [['sex'], 'string', 'max' => 10],
            [['email', 'email2'], 'email'],
            [['account_id'], 'number'],
            [['company_id', 'parent_company_id', 'customer_reference_id', 'publicity_shape', 'phone','phone2', 'phone3', 'screen_notification', 'sms_notification', 'email_notification', 'sms_fields_notifications', 'email_fields_notifications', '_notifications_way', '_sms_fields_notifications', '_email_fields_notifications', 'phone4', 'last_update', 'hourRanges' ], 'safe'],
            [['code', 'payment_code'], 'unique'],
            //['document_number', CuitValidator::className()],
            ['document_number', 'compareDocument'],
            [['last_calculation_current_account_balance', 'current_account_balance'], 'safe'],
            //['document_number', 'validateCustomer', 'on' => 'insert']
        ];

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
        if($this->document_type_id != 1) {
            $this->docNumberValidation();
            //$rules[] = ['document_number', 'compare', 'compareValue' => 999999, 'operator' => '>=', 'type' => 'number'];
        }

        return $rules;
    }

    public function compareDocument()
    {
        if($this->document_type_id != 1) {
            if($this->document_number <= 999999) {
                $this->addError('document_number', Yii::t('app', 'The document number must be geater than 999999.'));
            }

            if (count($this->getErrors($this->document_number)) > 8 && (integer)$this->document_number >=100000000) {
                $this->addError('document_number', Yii::t('app','The document number must be less than 100000000.'));
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
            'hourRanges' => Yii::t('app', 'Customer Hour range')
        ];

        //Labels adicionales definidos para los profiles
        $profiles = self::getEnabledProfileClasses();
        foreach ($profiles as $profile) {
            $labels['profile_' . $profile->profile_class_id] = $profile->name;
        }

        return $labels;
    }

    /**
     * @return ActiveQuery
     */
    public function getBills() {
        return $this->hasMany(Bill::className(), ['customer_id' => 'customer_id']);
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
                $log->createInsertLog($this->customer_id, 'Customer', $this->customer_id);
            } else {
                foreach ($changedAttributes as $attr => $oldValue) {
                    if ($this->$attr != $oldValue) {
                        if($attr == 'document_number' || $attr == 'email' || $attr == 'email2' || $attr == 'phone'  || $attr == 'phone2' || $attr == 'phone3' || $attr == 'phone4' || $attr == 'hourRanges') {
                            $this->updateAttributes(['last_update' => (new \DateTime('now'))->format('Y-m-d')]);
                        }

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
            $dateNow = (new DateTime('now'))->format('Y-m-d');
        }
        return $this
                        ->hasMany(CustomerHasDiscount::className(), ['customer_id' => 'customer_id'])
                        ->where("'" . $dateNow . "' between from_date and to_date")
                        ->andWhere(['status' => Discount::STATUS_ENABLED])
        ;
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
        if (parent::beforeSave($insert)) {
            
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
    public function getFullName($template = '{lastname}, {name}') {
        if (!empty($this->lastname)) {
            return str_replace(['{lastname}','{name}'], [$this->lastname, $this->name], $template);
        }else{
            $template = '{name}';
             return str_replace(['{lastname}','{name}'], [$this->lastname, $this->name], $template);
        }
       
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

        return $this->taxCondition->getBillTypes()->where(['bill_type.bill_type_id' => $billType->bill_type_id])->exists();
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

    public function updatePaymentCode()
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
                $this->payment_code < 0
            ) {
                $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
                $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . ($company->code == '9999' ? '' : '000' ) .
                    str_pad($this->code, 5, "0", STR_PAD_LEFT) ;
                $this->payment_code = $generator->generate($code);
            }
        } else {
            $this->payment_code = null;//-($this->code ? $this->code : rand());
        }
        $this->updateAttributes(['payment_code']);
        return $this->payment_code;
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

    public static function verifyEmails($data, $field = "email")
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
                $customers = Customer::find()->andWhere([$field => $row[0], 'status' => 'enabled'])->all();

                foreach ($customers as $customer) {
                    $status_field = $field.'_status';
                    $customer->$status_field = strtolower($row[1]);
                    $customer->updateAttributes([$status_field]);

                    $results['total']++;
                    $results[strtolower($row[1])]++;

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
        $customer_message = CustomerMessage::findOne($id_customer_message);
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

        foreach ($this->getContracts()->where(['status' => Contract::STATUS_ACTIVE])->all() as $contract) {
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
                if(!$this->canNotifyPayment() && $payment_extension_qty > 1) {
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
     * Indica si el cliente puede pedir una extension de pago
     */
    public function canRequestPaymentExtension()
    {
        //Sólo si el cliente no debe mas de una factura
        if(Customer::getOwedBills($this->customer_id) > (int)Config::getValue('payment_extension_debt_bills')) {
            return false;
        }

        //Y si no ha solicitado el máximo de extensiones de pago permitidas.
        $maximun_payment_extension_qty = Config::getValue('payment_extension_qty_per_month');
        $payment_extension_qty = $this->getPaymentExtensionQtyRequest();
        \Yii::trace($payment_extension_qty);

        return $payment_extension_qty < $maximun_payment_extension_qty ? true : false;
    }

    /**
     * Indica si el cliente puede informar de un pago.
     * Sólo puede informar de un nuevo pago si no ha hecho el informe de un pago este mes
     */
    public function canNotifyPayment()
    {
        $date = (new \DateTime('first day of this month'))->format('Y-m-d');
        $date_to = (new \DateTime('last day of this month'))->format('Y-m-d');

        return !$this->getNotifyPayments()->where(['>','date', $date])->andWhere(['<', 'date', $date_to])->exists();
    }
}