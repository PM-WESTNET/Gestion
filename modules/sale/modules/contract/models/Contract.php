<?php

namespace app\modules\sale\modules\contract\models;

use app\components\db\ActiveRecord;
use app\modules\config\models\Config;
use app\modules\sale\models\Address;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\models\Product;
use app\modules\sale\models\ProductToInvoice;
use app\modules\sale\modules\contract\components\CompanyByNode;
use app\modules\ticket\models\Category;
use app\modules\westnet\components\SecureConnectionUpdate;
use app\modules\westnet\isp\IspFactory;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\Vendor;
use webvimark\modules\UserManagement\models\User;
use yii\db\Query;
use yii\helpers\ArrayHelper;
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
                //  Se sobreescribe la propiedad events del behavior para evitar que se distare cuando el contrato se
                //inserta y que aun no tenga contract details que analizar.
                $behaviors[] = [
                    'class' => 'app\modules\westnet\components\MesaTicketContractBehavior',
                    'events' => []
                ];
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
            //[['instalation_schedule'], 'in', 'range' => ['in the morning', 'in the afternoon', 'all day']],
            //[['instalation_schedule'], 'default', 'value' => 'all day'],
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
        return $this->hasOne(Address::className(), ['address_id' => 'address_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractDetails() {
        return $this->hasMany(ContractDetail::className(), ['contract_id' => 'contract_id'])->cache(-1);
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts() {
        return $this->hasMany(Product::className(), ['product_id' => 'product_id'])->viaTable('contract_detail', ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractLogs() {
        return $this->hasMany(ContractLog::className(), ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getVendor() {
        //No modificar esta linea, es correcto de esta forma:
        return $this->hasOne(Vendor::className(), ['vendor_id' => 'vendor_id']);
    }

    public function getConnection() {
        return $this->hasOne(Connection::className(), ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLowCategory() {
        return $this->hasOne(Category::className(), ['category_id' => 'category_low_id']);
    }

    /**
     * Retorna los Detalles del contrato en base a un type de producto
     * @return ActiveQuery
     */
    public function getContractDetailsByType($types = [], $exclude = []) {
        $query = $this->hasMany(ContractDetail::className(), ['contract_id' => 'contract_id'])
                ->leftJoin('product p', 'contract_detail.product_id = p.product_id');

        if (!empty($types)) {
            $query->andWhere(['type' => $types]);
        }

        if (!empty($exclude)) {
            $query->andWhere(['not in', 'type', $exclude]);
        }

        return $query;
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
        $contractDetail->setAttributes($contractDetails);
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

        // Si comienza el proceso de baja, deshabilito el debito automatico de firstdata
        if ($this->status === self::STATUS_LOW_PROCESS) {
            $this->customer->inactiveFirstdataDebit();
        }


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
                if (User::hasPermission('contract-view')) {
                    return true;
                }
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
            ['status' => Contract::STATUS_DRAFT ],
            ['status' => Contract::STATUS_ACTIVE ],
            ['status' => Contract::STATUS_CANCELED ],
            ['status' => Contract::STATUS_INACTIVE ],
            ['status' => Contract::STATUS_LOW ],
            ['status' => Contract::STATUS_LOW_PROCESS ],
            ['status' => Contract::STATUS_NEGATIVE_SURVEY ],
            ['status' => Contract::STATUS_NO_WANT ],
        ];
    }

    public static function getStatusesForSelect(){
        $status_array = ArrayHelper::map(Contract::getStatuses(), 'status', 'status');
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
     * @param $period
     * @return int
     * @throws \Exception
     * Devuelve la cantidad de productos a facturar de extension de pago para el período dado (en caso de estar vacío, utiliza el período corriente) que aún no han sido facturados.
     * Regla de negocio: Para consultar las extensiones pedidas el mes corriente es necesario que el $period sea correspondiente al primer dia del mes siguiente, ya que las extensiones
     * de pago se agregan para la facturación del próximo mes.
     */
    public function getActivePaymentExtensionQtyPerPeriod($period = null)
    {
        $end_period = (new \DateTime($period))->modify('+1 month')->format('Y-m-01');
        $payment_extension_product_id = Config::getValue('id-product_id-extension-de-pago');

        if(!$period) {
            $period = (new \DateTime('now'))->format('Y-m-01');
        }

        $contract_detail_ids = (new Query())
            ->select('contract_detail_id')
            ->from('contract_detail')
            ->where(['contract_id' => $this->contract_id])
            ->andWhere(['product_id' => $payment_extension_product_id])
            ->all();

        return count(ProductToInvoice::find()
            ->where(['status' => ProductToInvoice::STATUS_ACTIVE])
            ->andWhere(['>=','period', $period])
            ->andWhere(['<', 'period', $end_period])
            ->andWhere(['in','contract_detail_id', $contract_detail_ids])
            ->all());
    }

    /**
     * Verifica si el contrato tiene un plan con la categoria de plan-fibra
     */
    public function hasFibraPlan()
    {
        $fibra_category = \app\modules\sale\models\Category::findOne(['system' => 'plan-fibra']);

        return $this->getContractDetails()
            ->leftJoin('product p', 'p.product_id = contract_detail.product_id')
            ->leftJoin('product_has_category phc', 'phc.product_id = p.product_id')
            ->where(['type' => 'plan'])
            ->andWhere(['phc.category_id' => $fibra_category->category_id])
            ->exists();
    }

    /**
     * Devuelve el importe de el producto extensión de pago
     */
    public function getAmountPaymentExtension()
    {
        $product = Product::findOne(Config::getValue('extend_payment_product_id'));

        return round($product->finalPrice,2);
    }

    public function hasPendingPlanChange()
    {
        return ProgrammedPlanChange::find()->andWhere(['contract_id' => $this->contract_id, 'applied' => 0])->exists();
    }

    public function getPendingPlanChange()
    {
        return ProgrammedPlanChange::find()
            ->andWhere(['contract_id' => $this->contract_id, 'applied' => 0])
            ->orderBy(['date' => SORT_DESC])
            ->one();
    }

    /**
     * ¡¡¡¡DANGER!!!!. Actualiza el contrato directamente contra el ISP
     * Usar con responsabilidad.
     * TODO: Ver la posibilidad de crear tests para probar esta función.
     * @return bool
     */
    public function updateOnISP()
    {
        if ($this->status === self::STATUS_ACTIVE) {
            return SecureConnectionUpdate::update($this->connection, $this, true);
        }

        return false;
    }

    /**
    * Return list contract by node_id
    */
    public static function findContractsByNode($node_id){
        return self::find()->leftJoin('connection con', 'con.contract_id = contract.contract_id')->where(['con.node_id' => $node_id, 'contract.status' => 'active', 'con.status' => 'enabled'])->all();
    }

    /**
     * Return all() contracts found by current customer_id
     */
    public function getAllContractsStatusesFromCurrentCustomer(){
        $contracts = (new Query())
            ->select('contract.status')
            ->from('contract')
            ->where(['customer_id' => $this->customer_id])
            ->column();

        return $contracts;
    }
}