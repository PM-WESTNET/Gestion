<?php

namespace app\modules\sale\modules\contract\models;

use app\components\db\ActiveRecord;
use app\modules\config\models\Config;
use app\modules\sale\models\Address;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\models\Product;
use app\modules\sale\modules\contract\components\CompanyByNode;
use app\modules\ticket\models\Category;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\Vendor;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "contract".
 *
 * @property integer $contract_id
 * @property integer $customer_id
 * @property string $date
 * @property string $from_date
 * @property string $to_date
 * @property string $status
 * @property integer $address_id
 * @property integer $vendor_id
 * @property integer $external_id
 * @property integer $tentative_node
 * @property string $instalation_schedule
 * @property integer $print_ads 
 * @property string $low_date
 * @property integer $category_low_id
 *
 * @property Address $address
 * @property Customer $customer
 * @property ContractDetail[] $contractDetails
 * @property Product[] $products
 * @property Vendor $vendor
 */
class Contract extends ActiveRecord {

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CANCELED = 'canceled';
    const STATUS_LOW_PROCESS = 'low-process';
    const STATUS_LOW = 'low';
    const STATUS_NO_WANT = 'no-want';
    const STATUS_NEGATIVE_SURVEY = 'negative-survey';

    private $_products;
    
    
    //Atributo usado en la activación, para enlazar con ADS
    public $customerCodeADS;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'contract';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::getBehaviors();

        if(!YII_ENV_TEST) {
            if (array_key_exists('contract_behaviors_status_change', Yii::$app->params)) {
                $behaviors = [
                    'status' => [
                        'class' => Yii::$app->params['contract_behaviors_status_change']
                    ],
                ];
            }

            $is_developer_mode = Config::getValue('is_developer_mode');
            if(!$is_developer_mode) {
                $behaviors[] = 'app\modules\westnet\components\MesaTicketContractBehavior';
                $behaviors[] = 'app\modules\westnet\components\RouterContractBehavior';
            }
        }

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules() {

        return [
            [['customer_id', 'address_id', 'vendor_id', 'external_id', 'tentative_node', 'print_ads', 'category_low_id'], 'integer'],
            [['date'], 'required'],
            [['date', 'from_date', 'to_date', 'address', 'customer', 'products', 'vendor', 'customerCodeADS', 'tentative_node', 'instalation_schedule', 'print_ads', 'low_date'], 'safe'],
            [['date', 'from_date', 'to_date', 'low_date'], 'date'],
            [['date'], 'default', 'value' => date('Y-m-d')],
            [['to_date', 'from_date'], 'default', 'value' => null],
            [['print_ads'], 'default', 'value' => 0],
            [['status'], 'string'],
            [['status'], 'in', 'range' => ['draft', 'active', 'inactive', 'canceled', 'low-process']],
            [['status'], 'default', 'value' => 'draft'],
            [['from_date'], 'required', 'on' => 'invoice'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'contract_id' => Yii::t('app', 'Contract'),
            'customer_id' => Yii::t('app', 'Customer'),
            'date' => Yii::t('app', 'Date'),
            'from_date' => Yii::t('app', 'From Date'),
            'to_date' => Yii::t('app', 'To Date'),
            'status' => Yii::t('app', 'Status'),
            'address_id' => Yii::t('app', 'Address'),
            'address' => Yii::t('app', 'Address'),
            'customer' => Yii::t('app', 'Customer'),
            'contractDetails' => Yii::t('app', 'ContractDetails'),
            'products' => Yii::t('app', 'Products'),
            'vendor' => Yii::t('app', 'Vendor'),
            'vendor_id' => Yii::t('westnet', 'Vendor'),
            'Address' => \Yii::t('app', 'Address'),
            'instalation_schedule' => Yii::t('app', 'Instalation Schedule'),
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['invoice'] = ['from_date'];
        $scenarios['cancel'] = ['to_date'];
        return $scenarios;
    }

    /**
     * @return ActiveQuery
     */
    public function getAddress() {
        return $this->hasOne(Address::class, ['address_id' => 'address_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractDetails() {
        return $this->hasMany(ContractDetail::class, ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     * Devuelve los detalles del contrato que esten en el rango de fecha, tomando como referencia el dia de hoy
     */
    public function getContractDetailsValidForToday() {
        $today = (new \DateTime('now'))->format('Y-m-d');
        return $this->hasMany(ContractDetail::class, ['contract_id' => 'contract_id'])
            ->where(['and',['<=', 'from_date', $today], ['>=', 'to_date', $today]])
            ->orWhere(['and',['<=', 'from_date', $today], ['to_date' => null]]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts() {
        return $this->hasMany(Product::class, ['product_id' => 'product_id'])->viaTable('contract_detail', ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractLogs() {
        return $this->hasMany(ContractLog::class, ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getVendor() {
        //No modificar esta linea, es correcto de esta forma:
        return $this->hasOne(Vendor::class, ['vendor_id' => 'vendor_id']);
    }

    public function getConnection() {
        return $this->hasOne(Connection::class, ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLowCategory() {
        return $this->hasOne(Category::class, ['category_id' => 'category_low_id']);
    }

    /**
     * Retorna los Detalles del contrato en base a un type de producto
     * @return ActiveQuery
     */
    public function getContractDetailsByType($types = [], $exclude = []) {
        $query = $this->hasMany(ContractDetail::class, ['contract_id' => 'contract_id'])
                ->leftJoin('product p', 'contract_detail.product_id = p.product_id');

        if (!empty($types)) {
            $query->andWhere(['in','type', $types]);
        }

        if (!empty($exclude)) {
            $query->andWhere(['not in', 'type', $exclude]);
        }

        return $query;
    }

    public function getLastContractDetailByType($type) {
        $today = (new \DateTime('now'))->format('Y-m-d');

        $query_base = $this->getContractDetails()
            ->leftJoin('product p', 'contract_detail.product_id = p.product_id');

        if ($type) {
            $query_base->where(['p.type' => $type]);
        }

        $query = clone($query_base);
        $query->andWhere(['and',['<=', 'from_date', $today], ['>=', 'to_date', $today]])
            ->orWhere(['and',['<=', 'from_date', $today], ['to_date' => null]]);

        if(empty($query->one())) {
            $query = $query_base
                ->orderBy(['contract_detail_id' => SORT_DESC]);
        }

        return $query->one();
    }

    /**
     * @brief Sets Products relation on helper variable and handles events insert and update
     */
    public function setProducts($products) {

        if (empty($products)) {
            $products = [];
        }

        $this->_products = $products;

        $saveProducts = function($event) {
            $this->unlinkAll('products', true);

            foreach ($this->_products as $id) {
                $this->link('products', Product::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveProducts);
        $this->on(self::EVENT_AFTER_UPDATE, $saveProducts);
    }

    /**
     * @inheritdoc
     * Strong relations: Address, Customer.
     */
    public function getDeletable() {
        if ($this->getAddress()->exists()) {
            return false;
        }
        if ($this->getCustomer()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Address, Customer, ContractDetails, Products.
     */
    protected function unlinkWeakRelations() {
        $this->unlinkAll('products', true);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    public function addContractDetail($contractDetails) {
        $contractDetail = new ContractDetail();
        $contractDetail->setAttributes($contractDetails, true);
        $this->link('contractDetails', $contractDetail);

        return $contractDetail;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        $this->formatDatesAfterFind();

        if ($insert) {
            $log = new CustomerLog();
            $log->createInsertLog($this->customer_id, 'Contract', $this->contract_id);
        } else {

            foreach ($changedAttributes as $attr => $oldValue) {
                if ($this->$attr !== $oldValue) {
                    switch ($attr){
                        case 'address_id':
                            $oldAddress= Address::findOne(['address_id' => $oldValue]);
                            $log = new CustomerLog();
                            $log->createUpdateLog($this->customer_id, $this->attributeLabels()['Address'], ($oldAddress == null ? '-' : $oldAddress->fullAddress), $this->address->fullAddress, 'Contract', $this->contract_id);
                            break;
                        case 'status':
                            if ($this->status === self::STATUS_CANCELED) {
                                $log= new CustomerLog();
                                $log->createUpdateLog($this->customer_id, $this->attributeLabels()[$attr], Yii::t('app', $oldValue), Yii::t('app', $this->status), 'Contract', $this->contract_id, 'Direccion MAC del equipo : ' . Yii::$app->request->get('mac_address'));
                            }else{
                                $log= new CustomerLog();
                                $log->createUpdateLog($this->customer_id, $this->attributeLabels()[$attr], Yii::t('app', $oldValue), Yii::t('app', $this->status), 'Contract', $this->contract_id);
                            }
                            break;
                        default:
                            $log = new CustomerLog();
                            $log->createUpdateLog($this->customer_id, $this->attributeLabels()[$attr], $oldValue, $this->$attr, 'Contract', $this->customer_id);
                            break;
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        if (empty($this->to_date) || $this->to_date == Yii::t('app', 'Undetermined time')) {
            $this->to_date = Yii::t('app', 'Undetermined time');
        } else {
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'dd-MM-yyyy');
        }
        if (empty($this->from_date)) {
            $this->from_date = Yii::t('app', 'Undetermined time');
        } else {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'dd-MM-yyyy');
        }

        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        
        if (empty($this->to_date) || $this->to_date == Yii::t('app', 'Undetermined time')) {
            $this->to_date = '';
        } else {
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd');
        }
        if (empty($this->from_date) || $this->from_date == Yii::t('app', 'Undetermined time') ) {
            $this->from_date = '';
        } else {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        }
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

    public static function getStatusRange() {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_CANCELED => Yii::t('app', 'Canceled'),
            self::STATUS_DRAFT => Yii::t('app', 'Draft'),
            self::STATUS_LOW_PROCESS => Yii::t('app', 'Low Process'),
            self::STATUS_LOW => Yii::t('app', 'Low'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
            self::STATUS_NO_WANT => Yii::t('app', 'No want'),
            self::STATUS_NEGATIVE_SURVEY => Yii::t('app', 'Negative survey')
        ];
    }

    public function getPlan() {
        foreach ($this->contractDetails as $contractDetail) {
            if ($contractDetail->product->type == 'plan') {
                return $contractDetail->product;
            }
        }

        return null;
    }

    public function isPlanChanged() {
        foreach ($this->contractDetails as $contractDetail) {
            if ($contractDetail->product->type == 'plan') {
                $lastLog = $contractDetail->getContractDetailLogs()->orderBy(['contract_detail_log_id' => SORT_DESC])->one();
                if ($lastLog->product_id != $contractDetail->product->product_id) {
                    return true;
                }
            }
        }

        return false;
    }
    
    
    public function canUpdate(){
        if (!User::hasRole('seller')) {
            if (User::hasPermission('update-contract') || User::hasRole('seller-office')) {
                return true;
            }else{
                return false;
            }
        }else{
            if (!User::hasRole('seller-office')) {
                if ($this->status !== self::STATUS_DRAFT) {
                    return false;
                }            
                
                return true;                
            }elseif (User::hasPermission('update-contract')) {
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function canView(){
         if (!User::hasRole('seller')) {
            if (User::hasPermission('contract-view') || User::hasRole('seller-office')) {
                return true;
            }else{
                return false;
            }
        }else{
            if (!User::hasRole('seller-office')) {
                $can = false;
                $vendor = Vendor::findOne(['user_id' => User::getCurrentUser()->id]);
                if(!$vendor) {
                    return false;
                }
                if ($this->vendor_id === $vendor->vendor_id) {
                    $can= true;
                }              
                
                return $can;
                
            }elseif (User::hasPermission('contract-view')) {
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function setTentativeNode($subnet_node){
        $node= Node::findOne(['subnet' => $subnet_node]);
        
        if (!empty($node)){
            $this->tentative_node = $subnet_node;
            $this->updateAttributes(['tentative_node']);
            if (!$this->customer->verifyCompany($node) ) {
                CompanyByNode::setCompanyToCustomer($node, $this->customer);
            }
            return true;
        }else{
            return false;
        }
    }

    public function canPrintAds(){
        return true;
    }
    
    public function getInstalationCharges(){
        if ($this->customer->customerCategory->name === "Familia") {
            $type= "category.system = 'instalacion-residencial'";
        }elseif ($this->customer->customerCategory->name === "Empresa"){
            $type="category.system = 'instalacion-empresa'";
        }else{
            $type="category.system = 'instalacion-empresa' OR category.system = 'instalacion-residencial'";
        }
        $charges= Product::find()
                ->where(['product.type' => 'product', 'product.status' => 'enabled'])
                ->joinWith('categories')
                ->andWhere($type)
                ->orderBy('product.name')
                ->all();
        
        return $charges;
    }

    public static function getStatuses(){
        return [
            self::STATUS_DRAFT => self::STATUS_DRAFT,
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
            self::STATUS_CANCELED => self::STATUS_CANCELED,
            self::STATUS_LOW_PROCESS => self::STATUS_LOW_PROCESS,
            self::STATUS_LOW => self::STATUS_LOW,
            self::STATUS_NO_WANT => self::STATUS_NO_WANT,
            self::STATUS_NEGATIVE_SURVEY => self::STATUS_NEGATIVE_SURVEY,
        ];
    }

    public static function getStatusesForSelect(){
        $status_array = Contract::getStatuses();
        foreach($status_array as $key => $value){
            $status_array[$key] = Yii::t('app',$value);
        }
        return $status_array;
    }

    /**
     * Revierte el estado de relevamiento negativo de un contrato
     * Si el contrato tiene conexiones pasa a estar en estado activo,
     * de no tener ninguna, queda en estado draft
     */
    public function revertNegativeSurvey()
    {
        if($this->getConnection()->exists()){
            $this->updateAttributes(['status' => 'active']);
        } else {
            $this->updateAttributes(['status' => 'draft']);
        }
    }

    /**
     * @throws \Exception
     * Cambia el estado de los detalles del contrato, que esten fuera del rango de fecha de hoy.
     */
    public function cancelContractDetailsOutOfDateRange()
    {
        $today = (new \DateTime('now'))->format('Y-m-d');
        $details = $this->getContractDetailsByType('plan')->andWhere(['<','to_date', $today])->all();

        foreach ($details as $detail) {
            $detail->updateAttributes(['status' => ContractDetail::STATUS_LOW]);
        }
    }
}