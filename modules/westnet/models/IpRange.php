<?php

namespace app\modules\westnet\models;

use app\modules\config\models\Config;
use Yii;
use IPv4\SubnetCalculator;

/**
 * This is the model class for table "ip_range".
 *
 * @property integer $ip_range_id
 * @property integer $ip_start
 * @property integer $ip_end
 * @property integer $last_ip
 * @property string $status
 * @property integer $node_id
 * @property string $type
 * @property integer $ap_id
 * 
 *
 * @property Node $node
 * @property AccessPoint $access_point
 */
class IpRange extends \app\components\db\ActiveRecord
{

    const NODE_SUBNET_TYPE = 'node_subnet';
    const NET_TYPE = 'net';
    const SUBNET_TYPE = 'subnet';
    const ENABLED_STATUS = 'enabled';
    const DISABLED_STATUS = 'disabled';
    const AVAILABLE_STATUS = 'available';


    public $net_address;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ip_range';
    }
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['node_id', 'ip_start', 'ip_end', 'last_ip' ], 'integer'],
            [['ip_start', 'ip_end', 'node'], 'required', 'on' => 'insert'],
            [['ip_start', 'ip_end'], 'required', 'on' => 'subnet-insert'],
            [['net_address'], 'required', 'on' => 'net-insert'],
            [['node', 'type', 'net_address'], 'safe'],
            [['type', 'net_address'], 'string'],
            [['status'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ip_range_id' => Yii::t('app', 'Network'),
            'ip_start' => Yii::t('westnet', 'Ip Start'),
            'ip_end' => Yii::t('westnet', 'Ip End'),
            'status' => Yii::t('westnet', 'Status'),
            'node_id' => Yii::t('westnet', 'Node ID'),
            'node' => Yii::t('westnet', 'Node'),
        ];
    }    

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::className(), ['node_id' => 'node_id']);
    }

    

    public function getIpAddresses()
    {
        return $this->hasMany(IpAddress::class, ['ip_range_id' => 'ip_range_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if ($this->type === self::NET_TYPE) {
            if ($this->getSubnets()->andWhere(['status' => self::AVAILABLE_STATUS])->exists()) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Node.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                $this->deleteSubnets();
                return true;
            }
        } else {
            return false;
        }
    }

    public function beforeSave($insert)
    {
        if ($insert && $this->type === self::NET_TYPE) {
            $this->calculateIpRange();
        }

        return parent::beforeSave($insert);
        
    }

    public function getIpStartFormatted()
    {
        return long2ip($this->ip_start);
    }

    public function getIpEndFormatted()
    {
        return long2ip($this->ip_end);
    }

    public function getAccessPoint() {
        return $this->hasOne(AccessPoint::class, ['ap_id' => 'access_point_id']);
    }
    public function getSubnets() {
        return IpRange::find()
            ->andWhere(['>=', 'ip_start', $this->ip_start])
            ->andWhere(['<=', 'ip_end', $this->ip_end])
            ->andWhere(['type' => self::SUBNET_TYPE]);
            
    }


    /*
        Indica si el rango tiene ips disponibles para asignar
    */
    public function hasAvailableIp()
    {

    }

    /*
        Devuelve una ip disponible para asignar
    */
    public function getAvailableIp()
    {
        $ip = null;

        $transaction = Yii::$app->db->beginTransaction();

        if ($this->last_ip === null) {
            $ip = $this->ip_start;

            if($this->isValid($ip)) {
                $this->updateAttributes(['status' => self::AVAILABLE_STATUS, 'last_ip' => $ip]);
                $transaction->commit();
                return $ip;
            }
        }else {
            $ip = $this->last_ip + 2;

            if ($ip > $this->ip_end) {
                $transaction->rollBack();
                return false;
            }
        }

        // Mientras no sea valida la ip seguimos calculando
        while (!$this->isValid($ip)) {
            $ip = $ip + 2;

            if ($ip > $this->ip_end) {
                $transaction->rollBack();
                return false;
            }
        }
        
        
        $this->updateAttributes(['last_ip' => $ip]);
        
        if ($ip === $this->ip_end) {
            $this->updateAttributes(['status' => self::DISABLED_STATUS]);
        }

        $transaction->commit();
        return $ip;
    }

    public function calculateIpRange()
    {
        if (!empty($this->net_address)) {
            $calculator = new SubnetCalculator($this->net_address, 16);

            $range = $calculator->getAddressableHostRange();

            $this->ip_start = ip2long($range[0]);
            $this->ip_end = ip2long($range[1]);

            for ($i = $this->ip_start; $i < $this->ip_end; ($i = $i + 256)) {
                $subnet = new IpRange();
                $subnet->ip_start = $i + (int)Config::getValue('ip_reserve_count');
                $subnet->ip_end = $i + 253;
                $subnet->type = self::SUBNET_TYPE;
                $subnet->status = self::ENABLED_STATUS;

                $subnet->scenario = 'subnet-insert';
                $subnet->save();
            }
        }
    }

    public function deleteSubnets() {
        IpRange::deleteAll(['AND', ['>=', 'ip_start', $this->ip_start], ['<=', 'ip_end', $this->ip_end]]);
    }

    public function getStatusLabel()
    {
        $labels = [
            'enabled' => Yii::t('app', 'Enabled'),
            'disabled' => Yii::t('app', 'Disabled'),
            'available' => Yii::t('app', 'Active'),
        ] ;

        return $labels[$this->status];
    }

    private function isValid($ip) 
    {
        return !Connection::find()->andWhere(['ip4_1' => $ip])->exists();
    }

}
