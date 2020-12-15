<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "ip_range".
 *
 * @property integer $ip_range_id
 * @property integer $ip_start
 * @property integer $ip_end
 * @property string $status
 * @property integer $node_id
 * @property string $type
 * 
 *
 * @property Node $node
 */
class IpRange extends \app\components\db\ActiveRecord
{

    const NODE_SUBNET_TYPE = 'node_subnet';
    const NET_TYPE = 'net';

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
            [['ip_start', 'ip_end'], 'required'],
            [['node', 'type'], 'safe'],
            [['type'], 'string'],
            [['status'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ip_range_id' => 'Ip Range ID',
            'ip_start' => Yii::t('westnet', 'Ip Start'),
            'ip_end' => Yii::t('westnet', 'Ip End'),
            'status' => Yii::t('westnet', 'Status'),
            'node_id' => Yii::t('westnet', 'Node ID'),
            'node' => Yii::t('westnet', 'Node'),
        ];
    }    

    public function validateIpsAndNode()
    {
        if ($this->type === self::NODE_SUBNET_TYPE) {
            if (empty($this->node_id)) {
                $this->addError('node_id', Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $this->attributeLabels()['node']]));
            }
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::className(), ['node_id' => 'node_id']);
    }

    public function getAccessPoint()
    {
        return $this->hasOne(AccessPoint::class, ['access_point_id' => 'access_point_id']);
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
                return true;
            }
        } else {
            return false;
        }
    }


    public function getIpStartFormatted()
    {
        return long2ip($this->ip_start);
    }

    public function getIpEndFormatted()
    {
        return long2ip($this->ip_end);
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

    }

}
