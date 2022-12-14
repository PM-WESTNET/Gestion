<?php

namespace app\modules\westnet\models;

use app\modules\zone\models\Zone;
use app\modules\westnet\models\IpRange;
use app\modules\westnet\models\NatServer;
use app\modules\sale\models\Company;
use app\modules\westnet\components\ipStrategy\AccessPointStrategy;
use Yii;
use app\modules\westnet\ecopagos\models\Ecopago;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\modules\westnet\components\ipStrategy\LegacyStrategy;

/**
 * This is the model class for table "node".
 *
 * @property integer $node_id
 * @property string $name
 * @property integer $zone_id
 * @property string $status
 * @property integer $subnet
 * @property integer $server_id
 * @property integer $parent_node_id
 * @property integer $has_ecopago_close
 * @property vlan
 * @property smartolt_olt_id;
 *
 * @property IpRange[] $ipRanges
 * @property Zone $zone
 * @property Server $server
 * @property NodeHasEcopago[] $nodeHasEcopagos
 * @property Connection[] $connections
 * @property Node $parentNode
 *
 */
class Node extends \app\components\db\ActiveRecord
{

    //public $ecopagos;
    private $_ecopagos;

    public $company_default;

    public $parent_company_id;
    public $company_id;
    public $count;
    private $_old_server_id;
    public $total;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'node';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['zone_id',  'server_id', 'parent_node_id', 'has_ecopago_close', 'vlan','nat_server_id', 'smartolt_olt_id'], 'integer'],
            [['zone', 'ecopagos', 'parentNode', 'companies', 'company_default', 'geocode'], 'safe'],
            [['zone_id', 'name', 'status', 'server_id'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 45],
            ['subnet', 'compare', 'compareValue' => 1, 'operator' => '>='],
            ['subnet', 'compare', 'compareValue' => 254, 'operator' => '<='],
            ['subnet', 'unique'],
            [['geocode'], 'geocodeValidation']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'node_id' => Yii::t('westnet', 'Id'),
            'name' => Yii::t('app', 'Name'),
            'zone_id' => Yii::t('app', 'Zone'),
            'status' => Yii::t('app', 'Status'),
            'server' => Yii::t('westnet', 'Server'),
            'server_id' => Yii::t('westnet', 'Server'),
            'zone' => Yii::t('app', 'Zone'),
            'subnet' => Yii::t('westnet', 'Subnet'),
            'parent_node' => Yii::t('westnet', 'Parent Node'),
            'parent_node_id' => Yii::t('westnet', 'Parent Node'),
            'has_ecopago_close' => Yii::t('westnet', 'Has Ecopago Close'),
            'geocode' => Yii::t('westnet', 'Geocode'),
            'vlan' => Yii::t('westnet', 'VLAN'),
            'smartolt_olt_id' => 'SmartOlt Olt ID'
        ];
    }

    public function geocodeValidation($attributeName, $params) {
        $geo = explode(',', str_replace(' ', '', $this->geocode));
        if(empty($this->geocode) || array_search( $this->geocode, [ '-34.66352,-68.35941,17', '-32.8988839,-68.8194614']) !== false ||
            count($geo) != 2 || ( count($geo) == 2 &&  (!is_numeric($geo[0]) || !is_numeric($geo[1])) )
        ) {
            $this->addError($attributeName, Yii::t('app', 'The geocode can\'t be empty or they have to be different.'));
            return false;
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::class, ['zone_id' => 'zone_id']);
    }

    //Devuelve el IpRange asignado al Nodo
    public function getIpRange()
    {
        return $this->hasOne(IpRange::class, ['node_id' => 'node_id']);

    }

    public function getNodeHasEcopago()
    {
        return $this->hasOne(NodeHasEcopago::class, ['node_id' => 'node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopagos()
    {
        return $this->hasMany(Ecopago::class, ['ecopago_id' => 'ecopago_id'])->viaTable('node_has_ecopago', ['node_id' => 'node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentNode()
    {
        return $this->hasOne(Node::class, ['node_id' => 'parent_node_id']);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServer()
    {
        return $this->hasOne(Server::class, ['server_id' => 'server_id']);
    }

    public function getConnections()
    {
        return $this->hasMany(Connection::class, ['node_id' => 'node_id']);
    }

    public function getAccessPoints()
    {
        return $this->hasMany(AccessPoint::class, ['node_id' => 'node_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNatServer()
    {
        return $this->hasOne(NatServer::class, ['nat_server_id' => 'nat_server_id']);
    }

    public function setEcopagos($ecopagos)
    {
        if (empty($ecopagos)) {
            $ecopagos = [];
        }
        $this->_ecopagos = $ecopagos;
        $saveEcopagos = function ($event) {
            //Quitamos las relaciones actuales
            $this->unlinkAll('ecopagos', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_ecopagos as $id) {
                $this->link('ecopagos', Ecopago::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveEcopagos);
        $this->on(self::EVENT_AFTER_UPDATE, $saveEcopagos);
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->_old_server_id = $this->server_id;

        parent::afterFind();
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
     * Weak relations: Zone, Company.
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
                $this->unLinkAll('ipRange', true);
                $this->unlinkAll('ecopagos', true);
                return true;
            }

        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($this->subnet)){
            $this->saveIpRange();
        }
        parent::afterSave($insert, $changedAttributes);

    }

    public function saveIpRange()
    {
        $ipRange = IpRange::findOne(['node_id' => $this->node_id]);
        if (empty($ipRange)) {
            $ipRange            = new IpRange();
            $ipRange->node_id   = $this->node_id;
        }

        $ipRange->ip_start  = ip2long('10.'.$this->subnet.'.3.2');
        $ipRange->ip_end    = ip2long('10.'.$this->subnet.'.254.254');

        if ($ipRange->isNewRecord) {
            return $ipRange->save();
        } else {
            return $ipRange->updateAttributes(['node_id', 'ip_start', 'ip_end']);
        }
    }

    /**
     * Retorna una ip usable para este nodo.
     *
     * @return int|mixed
     */
    public function getUsableIp($ap = null)
    {
        if (empty($ap)) {
            $strategy = new LegacyStrategy();
        } else {
            $strategy = new AccessPointStrategy();
        }

        return $strategy->getValidIp($this, $ap);
    }

    /**
     * 
     * Retorna una ip valida.
     * 
     * @deprecated
     * 
     * @param $start
     * @param $end
     * @return int
     */
    private function validIp($start, $end)
    {
        $validIp = false;
        do{
            // Genero un nro aleatorio para el rango
            $ip = rand($start, $end);
            // Si la ip es par, la hago impar
            $ip = (($ip%2)==0 ? $ip : $ip+1 );
            $nodo = ip2long('10.'.$this->subnet.'.0.0');
            $oct = $ip - $nodo;

            preg_match("/\.0$/", long2ip($oct) , $output);
            $validIp = !count($output);
        } while(!$validIp);

        $cant = (new Query())->from('connection')
            ->where("connection.ip4_1 - " . $nodo . " = " . $oct)
            ->count("*");


        if($cant==0) {
            return $ip;
        } else {
            return $this->validIp($start, $end);
        }
    }

    public function getFirstCompanies()
    {
        $companies = [];
        /** @var NodeHasCompanies $nhc */
        foreach($this->getCompanies()->all() as $nhc){
            $companies[] = $nhc->firstCompany;
        }

        return $companies;
    }

    /**
     * Devuelve un array con todos los nodos
     */
    public function getForSelect()
    {
        return ArrayHelper::map(Node::find()->all(), 'node_id', 'name');
    }


    /**
    * Return node by subnet
    */
    public static function findNodeBySubnet($subnet){
        return self::find()->where(['subnet' => $subnet])->one();
    }

    public static function FindNodeFieldsLatitudLongitud($node_id){
        return explode(',', self::find()->where(['node_id' => $node_id])->one()->geocode);
    }
}
