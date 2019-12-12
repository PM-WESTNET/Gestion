<?php

namespace app\modules\westnet\notifications\models;

use app\components\db\ActiveRecord;
use app\components\helpers\DbHelper;
use app\components\helpers\EmptyLogger;
use app\modules\config\models\Config;
use app\modules\sale\models\Company;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerCategory;
use app\modules\sale\models\CustomerClass;
use app\modules\sale\models\Product;
use app\modules\sale\models\search\CustomerSearch;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Node;
use app\modules\westnet\notifications\components\transports\EmailTransport;
use app\modules\westnet\notifications\NotificationsModule;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "destinatary".
 *
 * @property integer $destinatary_id
 * @property integer $notification_id
 * @property string $name
 * @property string $code
 * @property integer $all_subscribed
 * @property integer $all_unsubscribed
 * @property string $customer_status
 * @property string $contract_status
 * @property integer $overdue_bills_from
 * @property integer $overdue_bills_to
 * @property integer $customer_class_id
 * @property integer $debt_from
 * @property integer $debt_to
 * @property string $has_app
 *
 * @property Customer[] $customers
 * @property Notification $notification
 * @property Node[] $nodes
 * @property Company[] $companies
 * @property CustomerCategory[] $customerCategories
 * @property CustomerClass[] $customerClasses
 * @property Product[] $plans
 * @property DestinataryHasCustomerStatus[] $customerStatuses
 * @property DestinataryHasContractStatus[] $contractStatuses
 */
class Destinatary extends ActiveRecord {

    //TODO: Modificar acceso a traves de getNodes y setNodes
    public $_nodes = [];

    private$_customers = [];

    public $_companies= [];

    public $_customer_categories= [];

    public $_customer_class= [];

    public $_plans= [];

    public $_contract_statuses= [];

    public $_customer_statuses= [];



    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'destinatary';
    }

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbnotifications');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['notification_id', 'type'], 'required'],
            [['notification_id', 'all_subscribed', 'all_unsubscribed', 'overdue_bills_from', 'overdue_bills_to', 'contract_min_age', 'contract_max_age'], 'integer'],
            [['debt_from', 'debt_to'], 'double'],
            [['name'], 'string'],
            [['notification', '_nodes', '_companies', '_customer_categories', '_customer_class', '_plans', '_contract_statuses', '_customer_statuses', 'customers', 'has_app'], 'safe'],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'destinatary_id' => NotificationsModule::t('app', 'Destinatary'),
            'notification_id' => NotificationsModule::t('app', 'Notification'),
            'company_id' => Yii::t('app', 'Company'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'all_subscribed' => NotificationsModule::t('app', 'All subscribed'),
            'all_unsubscribed' => NotificationsModule::t('app', 'All unsubscribed'),
            'customer_status' => NotificationsModule::t('app', 'Customer status'),
            'contract_status' => NotificationsModule::t('app', 'Contract status'),
            'overdue_bills_from' => NotificationsModule::t('app', 'Overdue bills from'),
            'overdue_bills_to' => NotificationsModule::t('app', 'Overdue bills to'),
            'customers' => Yii::t('app', 'Customers'),
            'notification' => NotificationsModule::t('app', 'Notification'),
            '_nodes' => NotificationsModule::t('app', 'Nodes'),
            'nodes' => Yii::t('app', 'Nodes'),
            'customer_class_id' => Yii::t('app', 'Customer Class'),
            'type' => Yii::t('app', 'Type'),
            'plan_id' => Yii::t('app', 'Plan'),
            'contract_min_age' => Yii::t('app', 'Contract minimal age in months'),
            'contract_max_age' => Yii::t('app', 'Contract maximum age in months'),
            'debt_from' => NotificationsModule::t('app', 'Debt From'),
            'debt_to' => NotificationsModule::t('app', 'Debt To'),
            'has_app' => NotificationsModule::t('app', 'Has App'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContracts() {
        return $this->hasMany(Contract::className(), ['contract_id' => 'contract_id'])->viaTable('destinatary_has_customer', ['destinatary_id' => 'destinatary_id'])->with(['customer']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNotification() {
        return $this->hasOne(Notification::className(), ['notification_id' => 'notification_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNodes() {
        return $this->hasMany(Node::className(), ['node_id' => 'node_id'])->viaTable('destinatary_has_node', ['destinatary_id' => 'destinatary_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompanies() {
        return $this->hasMany(Company::class, ['company_id' => 'company_id'])->viaTable('destinatary_has_company', ['destinatary_destinatary_id' => 'destinatary_id']);
    }

    public function getCustomerCategories(){
        return $this->hasMany(CustomerClass::class, ['customer_class_id' => 'customer_category_id'])->viaTable('destinatary_has_customer_category', ['destinatary_destinatary_id' => 'destinatary_id']);
    }

    public function getCustomerClasses(){
        return $this->hasMany(CustomerCategory::class, ['customer_category_id' => 'customer_class_id'])->viaTable('destinatary_has_customer_class', ['destinatary_destinatary_id' => 'destinatary_id']);
    }

    public function getPlans(){
        return $this->hasMany(Product::class, ['product_id' => 'plan_id'])->viaTable('destinatary_has_plan', ['destinatary_destinatary_id' => 'destinatary_id']);
    }

    public function getContractStatuses(){
        return $this->hasMany(DestinataryHasContractStatus::class, ['destinatary_destinatary_id' => 'destinatary_id']);
    }

    public function getCustomerStatuses(){
        return $this->hasMany(DestinataryHasCustomerStatus::class, ['destinatary_destinatary_id' => 'destinatary_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        parent::beforeSave($insert);

        if ($insert) {

        } else {

        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        $this->addNodes();
        $this->addCompanies();
        $this->addCustomerCategory();
        $this->addCustomerClass();
        $this->addPlan();
        $this->addContractStatus();
        $this->addCustomerStatus();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {

        parent::afterFind();

        //Nodes for form manipulation
        if (!empty($this->nodes)) {
            foreach ($this->nodes as $node) {
                $this->_nodes[] = $node->node_id;
            }
        }

        if (!empty($this->customerStatuses)) {
            foreach ($this->customerStatuses as $customer_status) {
                $this->_customer_statuses[] = $customer_status->customer_status;
            }
        }

        if (!empty($this->contractStatuses)) {
            foreach ($this->contractStatuses as $contract_status) {
                $this->_contract_statuses[] = $contract_status->contract_status;
            }
        }

        if (!empty($this->customerCategories)) {
            foreach ($this->customerCategories as $customer_category) {
                $this->_customer_categories[] = $customer_category->customer_class_id;
            }
        }

        if (!empty($this->customerClasses)) {
            foreach ($this->customerClasses as $customer_class) {
                $this->_customer_class[] = $customer_class->customer_category_id;
            }
        }

        if (!empty($this->companies)) {
            foreach ($this->companies as $company) {
                $this->_companies[] = $company->company_id;
            }
        }

        if (!empty($this->plans)) {
            foreach ($this->plans as $plan) {
                $this->_plans[] = $plan->product_id;
            }
        }
    }

    /**
     * Returns an array with all node_id related to this destinatary model
     * @return array
     */
    public function getNodesIds() {

        if (empty($this->nodes))
            return [];

        $nodes = [];

        foreach ($this->nodes as $node) {
            $nodes[] = $node->node_id;
        }

        return $nodes;
    }

    public function getCompaniesIds() {

        if (empty($this->companies))
            return [];

        $companies = [];

        foreach ($this->companies as $comp) {
            $companies[] = $comp->company_id;
        }

        return $companies;
    }

    public function getCustomerClassesIds() {

        if (empty($this->customerClasses))
            return [];

        $customer_classes = [];

        foreach ($this->customerClasses as $class) {
            $customer_classes[] = $class->customer_category_id;
        }

        return $customer_classes;
    }

    public function getCustomerCategoriesIds() {

        if (empty($this->customerCategories))
            return [];

        $customer_categories = [];

        foreach ($this->customerCategories as $cat) {
            $customer_categories[] = $cat->customer_class_id;
        }

        return $customer_categories;
    }

    public function getPlansIds() {

        if (empty($this->plans))
            return [];

        $plans = [];

        foreach ($this->plans as $plan) {
            $plans[] = $plan->product_id;
        }

        return $plans;
    }

    public function getContractStatusesLabels() {

        if (empty($this->contractStatuses)){
            return [];
        }

        $statuses = [];

        foreach ($this->contractStatuses as $status) {
            $statuses[] = $status->contract_status  ;
        }

        return $statuses;
    }

    public function getCustomerStatusesLabels()
    {

        if (empty($this->customerStatuses)){
            return [];
        }

        $statuses = [];

        foreach ($this->customerStatuses as $status) {
            $statuses[] = $status['customer_status'];
        }

        return $statuses;
    }

    public function getCompaniesObject()
    {
        $ids= $this->companiesIds;

        $companies = Company::find()->where(['company_id' => $ids])->all();

        return $companies;
    }

    /**
     * Deletes all associated nodes and creates new ones from form data
     * @param array $nodes
     */
    private function addNodes() {
        if (!empty($this->nodes)) {
            $this->unlinkAll('nodes', true);
        }

        if (!empty($this->_nodes)) {
            foreach ($this->_nodes as $node) {
                $newNode = new DestinataryHasNode();
                $newNode->node_id = $node;
                $newNode->destinatary_id = $this->destinatary_id;
                $newNode->save();
            }
        }
    }

    private function addCompanies() {
        $this->unlinkAll('companies', true);
        if (!empty($this->_companies)) {
            foreach( $this->_companies as $company_id ) {
                $newCompany = new DestinataryHasCompany();
                $newCompany->company_id = $company_id;
                $newCompany->destinatary_destinatary_id = $this->destinatary_id;
                $newCompany->save();
            }
        }
    }

    private function addCustomerCategory() {
        if (!empty($this->customerCategories)) {
            $this->unlinkAll('customerCategories', true);
        }

        if (!empty($this->_customer_categories)) {
            foreach ($this->_customer_categories as $cat) {
                $newCategory = new DestinataryHasCustomerCategory();
                $newCategory->customer_category_id = $cat;
                $newCategory->destinatary_destinatary_id = $this->destinatary_id;
                $newCategory->save();
            }
        }
    }

    private function addCustomerClass() {
        if (!empty($this->customerClasses)) {
            $this->unlinkAll('customerClasses', true);
        }

        if (!empty($this->_customer_class)) {
            foreach ($this->_customer_class as $class) {
                $newClass = new DestinataryHasCustomerClass();
                $newClass->customer_class_id = $class;
                $newClass->destinatary_destinatary_id = $this->destinatary_id;
                $newClass->save();
            }
        }
    }

    private function addPlan() {
        if (!empty($this->plans)) {
            $this->unlinkAll('plans', true);
        }

        if (!empty($this->_plans)) {
            foreach ($this->_plans as $plan) {
                $newPlan = new DestinataryHasPlan();
                $newPlan->plan_id = $plan;
                $newPlan->destinatary_destinatary_id = $this->destinatary_id;
                $newPlan->save();
            }
        }
    }

    private function addContractStatus() {
        if (!empty($this->contractStatuses)) {
            $this->unlinkAll('contractStatuses', true);
        }

        if (!empty($this->_contract_statuses)) {
            foreach ($this->_contract_statuses as $status) {
                $newContractStatus = new DestinataryHasContractStatus();
                $newContractStatus->contract_status = $status;
                $newContractStatus->destinatary_destinatary_id = $this->destinatary_id;
                $newContractStatus->save();
            }
        }
    }

    private function addCustomerStatus() {
        if (!empty($this->customerStatuses)) {
            $this->unlinkAll('customerStatuses', true);
        }

        if (!empty($this->_customer_statuses)) {
            foreach ($this->_customer_statuses as $status) {
                $newCustomerStatus = new DestinataryHasCustomerStatus();
                $newCustomerStatus->customer_status = $status;
                $newCustomerStatus->destinatary_destinatary_id = $this->destinatary_id;
                $newCustomerStatus->save();
            }
        }
    }


    /**
     * Add all selected contracts to this destinatary instance
     * @param array $contracts
     * @return boolean
     */
    public function addContracts($contracts = []) {

        if (empty($contracts))
            return false;

        $this->unlinkAll('contracts', true);

        foreach ($contracts as $contract_id) {
            $dhc = new DestinataryHasContract();
            $dhc->destinatary_id = $this->destinatary_id;
            $dhc->contract_id = $contract_id;
            $dhc->save();
        }
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Customers, Notification, Nodes.
     */
    protected function unlinkWeakRelations() {
        $this->unlinkAll('customers', true);
        $this->unlinkAll('nodes', true);
        $this->unlinkAll('companies', true);
        $this->unlinkAll('customerStatuses', true);
        $this->unlinkAll('contractStatuses', true);
        $this->unlinkAll('customerClasses', true);
        $this->unlinkAll('customerCategories', true);
        $this->unlinkAll('plans', true);
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

    /**
     * Devuelve un query para obtener los clientes que aplican al formulario actual
     * @return Query
     */
    public function getCustomersQuery($normalQuery = true)
    {
        $search = new CustomerSearch;
        $params = ['CustomerSearch'];


        //Query armado de acuerdo a parametros
        $params['CustomerSearch'] = [
            'nodes' => $this->getNodesIds(),
            'company_id' => $this->getCompaniesIds(),
            'customer_status' => $this->getCustomerStatusesLabels(),
            'customer_class_id' => $this->getCustomerCategoriesIds(),
            'customer_category_id' => $this->getCustomerClassesIds(),
            'contract_status' => $this->getContractStatusesLabels(),
            'contract_min_age' => $this->contract_min_age,
            'contract_max_age' => $this->contract_max_age,
            'plan_id' => $this->getPlansIds()
        ];


        if($this->type == 'by_customers') {
            foreach( $this->getCustomers()->select(['customer_id'])->asArray()->all() as $customer) {
                $params['CustomerSearch']['customers_id'][] = $customer['customer_id'];
            }
        }

        if($this->overdue_bills_from) {
            $params['CustomerSearch']['debt_bills_from'] = $this->overdue_bills_from;
        }

        if($this->overdue_bills_to) {
            $params['CustomerSearch']['debt_bills_to'] = $this->overdue_bills_to;
        }

        // Si estoy filtrando con saldo positivo.
        if($this->debt_from<0) {
            $params['CustomerSearch']['amount_due_to'] = $this->debt_from;
        } else {
            $params['CustomerSearch']['amount_due'] = $this->debt_from;
            $params['CustomerSearch']['amount_due_to'] = (is_null($this->debt_to) ? 0 : $this->debt_to);
            $params['CustomerSearch']['amount_due_to'] = (!$params['CustomerSearch']['amount_due_to'] ? 100000: $params['CustomerSearch']['amount_due_to']);
        }

        $query = $search->buildDebtorsQuery($params);

        // Filtro de instalacion de app
        $this->filterMobileAppStatus($query);

        $subquery = new Query();
        $subquery->select("im.customer_id as customer_integratech, im.status as status_integratech, im.integratech_message_id")
                ->from(DbHelper::getDbName(Yii::$app->dbnotifications).".integratech_message im")
                ->leftJoin(DbHelper::getDbName(Yii::$app->dbnotifications).".notification n", "im.notification_id = n.notification_id")
                ->where("n.status = 'enabled' AND im.status = 'pending' ");

        $query->from['b']->addSelect(['connection.ip4_1 as ipv4', 'customer.email', 'customer.email_status', 'customer.phone2',
            'customer.phone3', 'customer.phone4', 'n.name as node', 'customer.payment_code', 'company.code as company_code',
            'connection.status_account as status', 'cc.name as category', 'customer.lastname']);

        $query->leftJoin(['n' => $subquery], 'b.customer_id = n.customer_integratech');


        return $query;
    }

    /**
     * Devuelve la lista de ips indexada por "c" + customer_id.
     * @return type
     */
    public function getIps()
    {
        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());

        //Obtenemos la query de deudores y le agregamos una condicion
        $query = $this->getCustomersQuery();
        $query->andWhere(new Expression('ipv4 IS NOT NULL and ipv4 <> 0'));

        $ips = [];

        //Batch para obtener ips
        foreach($query->each() as $customer){
            $ips['i'.$customer['ipv4']] = long2ip($customer['ipv4']);
        }

        return $ips;
    }

    /**
     * Devuelve la lista de emails indexada por email, con el nombre del customer
     * como value.
     * @return type
     */
    public function getEmails()
    {
        //Para evitar que la memoria alcance el limite
        Yii::setLogger(new EmptyLogger());

        //Obtenemos la query de deudores y le agregamos una condicion
        $query = $this->getCustomersQuery();
        $query->andWhere(['IS NOT','b.email',  NULL]);
        if ($this->notification->transport->class === EmailTransport::class) {
            $query->andWhere(['email_status' => 'active']);
        }
        $emails = [];

        //Batch para obtener emails
        foreach($query->each() as $customer){
            $emails[$customer['email']] = $customer;
        }

        return $emails;
    }

    public function setCustomers($customers)
    {
        if(empty($customers)){
            $customers = [];
        }

        $this->_customers = $customers;

        $save = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('customers', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_customers as $id){
                $this->link('customers', Customer::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $save);
        $this->on(self::EVENT_AFTER_UPDATE, $save);
    }

    public function getCustomers()
    {
        return $this->hasMany(Customer::class, ['customer_id' => 'customer_id'])->viaTable('destinatary_has_customer', ['destinatary_id' => 'destinatary_id']);
    }

    /**
     * @param $query
     * @throws \Exception
     * Filtra por el estado de la aplicación móvil
     */
    private function filterMobileAppStatus($query){
        $uninstalled_period = Config::getValue('month-qty-to-declare-app-uninstalled');
        $date_min_last_activity = (new \DateTime('now'))->modify("-$uninstalled_period month")->getTimestamp();

        //Si la notificacon es mobile push si o si necesitamos clientes que posean la app instalada
        if ($this->notification->transport->slug === 'mobile-push'){
            $query->leftJoin('user_app_has_customer uahc', 'uahc.customer_id = b.customer_id')
                ->leftJoin('user_app ua', 'ua.user_app_id = uahc.user_app_id')
                ->leftJoin('user_app_activity uaa', 'uaa.user_app_id = ua.user_app_id');

            $query->andFilterWhere(['not',['uahc.customer_id' => null]])
                ->andFilterWhere(['not',['uaa.user_app_id' => null]])
                ->andFilterWhere(['>=','uaa.last_activity_datetime', $date_min_last_activity]);
        }else {
            if(!empty($this->has_app)) {
                $query->leftJoin('user_app_has_customer uahc', 'uahc.customer_id = b.customer_id')
                    ->leftJoin('user_app ua', 'ua.user_app_id = uahc.user_app_id')
                    ->leftJoin('user_app_activity uaa', 'uaa.user_app_id = ua.user_app_id');

                if($this->has_app == 'not_installed') {
                    $query->andFilterWhere(['not',['uahc.customer_id' => null]])
                        ->andFilterWhere(['not',['uaa.user_app_id' => null]])
                        ->andFilterWhere(['<=','uaa.last_activity_datetime', $date_min_last_activity]);

                    $query->orWhere(['uahc.customer_id' => null]);
                }

                if($this->has_app == 'installed') {
                    $query->andFilterWhere(['not',['uahc.customer_id' => null]])
                        ->andFilterWhere(['not',['uaa.user_app_id' => null]])
                        ->andFilterWhere(['>=','uaa.last_activity_datetime', $date_min_last_activity]);
                }
            }
        }


    }
}
