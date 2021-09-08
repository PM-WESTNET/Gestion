<?php

namespace app\modules\westnet\models;

use app\components\db\ActiveRecord;
use app\modules\config\models\Config;
use app\modules\sale\models\CustomerLog;
use app\modules\sale\modules\contract\components\CompanyByNode;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\modules\westnet\models\search\ConnectionForcedHistorialSearch;
use Codeception\Util\Debug;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\console\Exception;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "connection".
 *
 * @property integer $connection_id
 * @property integer $contract_id
 * @property integer $node_id
 * @property integer $server_id
 * @property integer $ip4_1
 * @property integer $ip4_2
 * @property integer $ip4_public
 * @property string $status
 * @property string $due_date
 * @property string $payment_code
 * @property string $status_account
 * @property integer $clean
 * @property integer $old_server_id
 * @property integer $access_point_id
 * @property integer $mac_address
 *
 * @property Contract $contract
 * @property Node $node
 * @property Server $server
 */
class Connection extends ActiveRecord {

    const SCENARIO_NEW = 'new';
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
    const STATUS_LOW= 'low';
    const STATUS_ACCOUNT_ENABLED = 'enabled';
    const STATUS_ACCOUNT_DISABLED = 'disabled';
    const STATUS_ACCOUNT_FORCED = 'forced';
    const STATUS_ACCOUNT_DEFAULTER = 'defaulter';
    const STATUS_ACCOUNT_CLIPPED = 'clipped';
    const STATUS_ACCOUNT_LOW= 'low';

    public $use_second_ip;
    public $has_public_ip;
    public $payment_code;
    public $old_status_account;
    public $old_node_id;
    public $old_server_id;

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_NEW] = ['contract_id', 'status'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'connection';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'status' => [
                'class' => 'app\modules\westnet\components\SecureConnectionBehavior',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['contract_id'], 'required'],
            [['contract_id', 'node_id', 'server_id', 'ip4_1', 'ip4_2', 'clean', 'old_server_id', 'access_point_id'], 'integer'],
            [['status', 'ip4_public', 'status_account'], 'string'],
            [['due_date', 'contract', 'node', 'server', 'use_second_ip', 'has_public_ip', 'access_point_id', 'mac_address' ], 'safe'],
            [['due_date'], 'date'],
            ['node_id', 'required', 'on' => self::SCENARIO_DEFAULT],
            [['payment_code'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'connection_id' => Yii::t('app', 'Connection ID'),
            'contract_id' => Yii::t('app', 'Contract ID'),
            'node_id' => Yii::t('westnet', 'Node'),
            'server_id' => Yii::t('app', 'Server ID'),
            'ip4_1' => Yii::t('westnet', 'Ip4 1'),
            'ip4_2' => Yii::t('westnet', 'Ip4 2'),
            'ip4_public' => Yii::t('westnet', 'Ip4 Public'),
            'status' => Yii::t('app', 'Status'),
            'due_date' => Yii::t('app', 'Due Date'),
            'contract' => Yii::t('app', 'Contract'),
            'node' => Yii::t('westnet', 'Node'),
            'server' => Yii::t('westnet', 'Server'),
            'payment_code' => Yii::t('app', 'Payment Code'),
            'status_account' => Yii::t('app', 'Status Account'),
            'clean' => Yii::t('app', 'Clean'),
            'access_point_id' => Yii::t('app', 'Access Point'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContract() {
        return $this->hasOne(Contract::class, ['contract_id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNode() {
        return $this->hasOne(Node::class, ['node_id' => 'node_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServer() {
        return $this->hasOne(Server::class, ['server_id' => 'server_id']);
    }

    public function getAccessPoint()
    {
        return $this->hasOne(AccessPoint::class, ['access_point_id' => 'access_point_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();

            // Pongo el estado en base al estado de cuenta.
            $this->status = ( $this->status_account == Connection::STATUS_ACCOUNT_DISABLED ?
                            Connection::STATUS_DISABLED : Connection::STATUS_ENABLED );

            /* $this->status = ( ( $this->status_account == Connection::STATUS_ACCOUNT_CLIPPED ||
              $this->status_account == Connection::STATUS_ACCOUNT_DISABLED ) ?
              Connection::STATUS_DISABLED : Connection::STATUS_ENABLED ); */

            // Pongo en formato long las ips que no son obligatorias
            $this->ip4_public = ($this->has_public_ip ? ip2long($this->ip4_public) : 0 );
            $this->ip4_2 = ($this->use_second_ip ? $this->ip4_1 + 1 : 0 );

            $company_code = '';
            $customer = $this->contract->customer;

            // Si no tiene company le asigno la del nodo
            if(!$customer->company && $this->node) {
                CompanyByNode::setCompanyToCustomer($this->node, $customer);
            }
            $company = $customer->company;

            $company_code = '';
            if($company) {
                // Si company existe traigo el prefijo para el codigo, sino lo saco del nodo
                $company_code = $company->code;
            }

            // Busco el codigo del cliente.
            $customerCode = $customer->code;

            // Busco la cantidad de conexiones para tener el digito.
            $query = new Query();
            $query
                    ->from('customer c')
                    ->leftJoin('contract ctr', 'c.customer_id = ctr.customer_id')
                    ->leftJoin('connection con', 'con.contract_id = ctr.contract_id')
                    ->where(['c.customer_id' => $customer->customer_id]);

            if ($this->isNewRecord) {
                $rightCode = $query->count('*');
            } else {
                $i = 0;
                $connections = $query->select(['con.*'])->all();
                foreach ($connections as $key => $value) {
                    if ($this->connection_id == $value['connection_id']) {
                        $rightCode = $i;
                        break;
                    }
                    $i++;
                }
            }

            // Genero el codigo
            $this->payment_code = str_pad($company_code, 4, "0", STR_PAD_LEFT) . ($company_code == '9999' ? '' : '000' ) .
                    str_pad($customerCode, 5, "0", STR_PAD_LEFT) . str_pad($rightCode, 2, "0", STR_PAD_LEFT);

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        try {
            if (!YII_ENV_TEST && $insert) {
                $log = new CustomerLog();
                $log->createInsertLog($this->contract->customer_id, 'Connection', $this->connection_id);
            } else if (!YII_ENV_TEST){

                foreach ($changedAttributes as $attr => $oldValue) {
                    $newValue = $this[$attr];
                    if ($newValue != $oldValue) {
                        switch ($attr) {
                            case 'node_id':
                                $oldNode = Node::findOne(['node_id' => $oldValue]);
                                $log = new CustomerLog();
                                $log->createUpdateLog($this->contract->customer_id, $this->attributeLabels()['node'], ($oldNode == null ? '-' : $oldNode->name), $this->node->name, 'Conexion', $this->connection_id);
                                break;
                            case 'server_id':
                                $oldServer = Server::findOne(['server_id' => $oldValue]);
                                $log = new CustomerLog();
                                $log->createUpdateLog($this->contract->customer_id, $this->attributeLabels()['server'], ($oldServer == null ? '-' : $oldServer->name), $this->server->name, 'Conexion', $this->connection_id);
                                break;
                            default:
                                $log = new CustomerLog();
                                if ($attr == 'status_account' && $this->status_account == 'forced') {
                                    $obs = 'Motivo: ' . Yii::$app->request->post('reason');
                                    $log->createUpdateLog($this->contract->customer_id, $this->attributeLabels()[$attr], Yii::t('westnet', ucfirst($oldValue)), Yii::t('westnet', ucfirst($newValue)), 'Conexion', $this->connection_id, $obs);

                                }elseif ($attr == 'status_account' || $attr == 'status' ) {
                                    $log->createUpdateLog($this->contract->customer_id, $this->attributeLabels()[$attr], Yii::t('westnet', ucfirst($oldValue)), Yii::t('westnet', ucfirst($newValue)), 'Conexion', $this->connection_id);
                                }elseif($attr == 'ip4_1' || $attr == 'ip4_2' || $attr == 'ip4_public'){
                                    $log->createUpdateLog($this->contract->customer_id, $this->attributeLabels()[$attr], (!empty($oldValue) ? long2ip($oldValue): '-'), long2ip($newValue), 'Conexion', $this->connection_id);
                                }else {
                                    $log->createUpdateLog($this->contract->customer_id, $this->attributeLabels()[$attr], $oldValue, $newValue, 'Conexion', $this->connection_id);
                                }
                                break;
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage() . '\n'. $ex->getTraceAsString());
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        $this->use_second_ip = ($this->ip4_2 != 0);
        $this->old_status_account = $this->status_account;
        $this->old_node_id = $this->node_id;

        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->due_date = ($this->due_date ? Yii::$app->formatter->asDate($this->due_date) : null);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        if ($this->due_date) {
            $this->due_date = Yii::$app->formatter->asDate($this->due_date, 'yyyy-MM-dd');
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
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Contract, Node, Server.
     */
    protected function unlinkWeakRelations() {
        
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

    public function getIp41Formatted() {
        return long2ip($this->ip4_1);
    }

    public function getIp42Formatted() {
        return long2ip($this->ip4_2);
    }

    public function getIp4PublicFormatted() {
        return long2ip($this->ip4_public);
    }

    /**
     * Actualiza la IP en base al nodo que tiene asignado
     */
    public function updateIp() {
        $node = $this->node;
        $ap = $this->accessPoint;
        $this->server_id = $node->server_id;

        $plan = $this->FindPlanConnection($this->contract->contract_id);
        if(!empty($plan) && $plan['big_plan']){
            $node = Node::findNodeBySubnet(235);
            $this->ip4_1 = $node->getUsableIp($ap);
        }else{
            $this->ip4_1 = $node->getUsableIp($ap);
        }

       
        
    }

    /**
     * Determina si se ha cambiado de nodo
     * @return bool
     */
    public function isNodeChanged() {
        return ($this->node_id != $this->old_node_id) && !empty($this->old_node_id);
    }

    public function revertChangeNode() {
        $this->node_id = $this->old_node_id;
    }

    /**
     * Determina si se ha cambiado de servidor
     * @return bool
     */
    public function isServerChanged() {
        return ($this->server_id != $this->node->server_id);
    }

    /**
     * Verifica si se puede forzar esta conexión
     * @return boolean
     */
    public function canForce(){

        //TODO: Remover esta condicion cuando se haya finalizado el desarrollo de IVR
        if ($this->contract->customer->code === 27237){
            return true;
        }

        $lastForced = $this->contract->customer->getLastForced();
        $timeBetween = (int)Config::getValue('time_between_payment_extension');

        if ($lastForced && ($lastForced->create_timestamp > (time() - ($timeBetween * 60)))) {
            return false;
        }

        $forcedHistoralSearch = new ConnectionForcedHistorialSearch();
        $forced_param = Config::getValue('times_forced_conn_month');
        $times = $forcedHistoralSearch->countForcedTimesForConnection($this->connection_id);
        
                
        if ((int)$times < (int)$forced_param && $this->status === self::STATUS_ENABLED && $this->contract->status === Contract::STATUS_ACTIVE) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Fuerza la conexion. Crea el contractDetail con el recargo y crea el producto a facturar
     * @param $data
     */
    public function force($due_date, $product_id, $vendor_id, $create_product = true)
    {
        $this->status_account = self::STATUS_ACCOUNT_FORCED;
        $this->due_date = $due_date;

        $trasanction = Yii::$app->db->beginTransaction();

        if ($this->save(true)) {
            $this->createForcedHistorial();
            if ($create_product){

                if (!$this->createExtendPaymentCD($product_id, $vendor_id)){
                    Debug::debug('O no crea CD o lo crea y sale por falso');
                    $trasanction->rollBack();
                    return false;
                }

                Debug::debug('----- antes'.count($this->contract->contractDetails));


                $cti = new ContractToInvoice();
                if (!$cti->updateContract($this->contract)) {
                    Debug::debug('No actualiza contrato');
                    $trasanction->rollBack();
                    return false;
                }
                Debug::debug('En teoria activa contrato');
            }

            $trasanction->commit();
            return true;
        }

        Yii::info(print_r($this->getErrors(), 1));
        Debug::debug('No update transaction');

        $trasanction->rollBack();
        return false;

    }

    /**
     * Create el Contract Detail del recargo por forzar conexion
     * @param $product_id
     * @return bool
     * @throws \Exception
     */
    private function createExtendPaymentCD($product_id, $vendor_id)
    {
        $contract_detail = new ContractDetail();
        $contract_detail->contract_id = $this->contract->contract_id;
        $contract_detail->product_id = $product_id;
        $contract_detail->count = 1;
        $contract_detail->from_date = (new \DateTime())->modify('first day of next month')->format('d-m-Y');
        $contract_detail->to_date = (new \DateTime())->modify('last day of next month')->format('d-m-Y');
        $contract_detail->vendor_id = $vendor_id;
        $contract_detail->status = Contract::STATUS_DRAFT;

        return $contract_detail->save(false);
    }

    private function createForcedHistorial() {
        $forcedHistory= new ConnectionForcedHistorial();
        $forcedHistory->date= date('Y-m-d');
        $forcedHistory->reason = Yii::$app->request->post('reason');
        $forcedHistory->connection_id= $this->connection_id;

        if (!empty(Yii::$app->user->identity)){
            $forcedHistory->user_id = Yii::$app->user->identity->id;
        }else{
            $superadmin = User::findOne(['username' => 'superadmin']);
            $forcedHistory->user_id = $superadmin->id;
        }


        $forcedHistory->save(false);
    }

    public static function changeNode(Connection $connection, $destination_node_id)
    {
        if ($connection->node_id != $destination_node_id) {
            $node = Node::findOne(['node_id'=> $destination_node_id]);
            $connection->old_server_id = $connection->server_id;
            $connection->server_id = $node->server_id;
            $connection->node_id = $node->node_id;

            ///?
            try {
                Yii::$app->formatter->asDate($connection->due_date, 'yyyy-MM-dd');
            } catch (\Exception $ex) {
                $connection->due_date = null;
            }
            $connection->updateIp();

            return $connection->save();
//            if ($connection->save()) {
//                $response = [
//                    'status' => 'success'
//                ];
//            } else {
//                $response = [
//                    'status' => 'error',
//                    'message' => Yii::t('westnet', 'Can\'t change the Node.')
//                ];
//            }
        }

        return false;
//        else {
//            $response = [
//                'status' => 'error',
//                'message' => Yii::t('westnet', 'The Node is already assigned.')
//            ];
//        }
    }

    /**
    * Return cantidad de conexiones activas
    */
    public static function FindConnectionsByNode($node_id){
        return self::find()->where(['node_id' => $node_id, 'status' => 'enabled'])->all();
    }


    /**
    * Return plan asociado al id del contrato
    */
    public static function FindPlanConnection($contract_id){

        return Yii::$app->db->createCommand(
                'SELECT * FROM contract_detail cd 
                 LEFT JOIN product pr ON pr.product_id = cd.product_id
                 WHERE pr.type = "plan" AND cd.contract_id = :contract_id;
            ')
            ->bindValue('contract_id',$contract_id)
            ->queryOne();
    }
}
