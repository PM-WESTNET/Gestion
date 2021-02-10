<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "access_point".
 *
 * @property int $access_point_id
 * @property string $name
 * @property string $status
 * @property string $strategy_class
 * @property int $node_id
 *
 * @property Node $node
 * @property IpRange[] $ipRanges
 */
class AccessPoint extends \yii\db\ActiveRecord
{

    public $ranges;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_point';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status', 'strategy_class', 'node_id'], 'required'],
            [['status'], 'string'],
            [['node_id'], 'integer'],
            [['name'], 'string', 'max' => 90],
            [['strategy_class'], 'string', 'max' => 255],
            [['ranges'], 'safe'],
            [['node_id'], 'exist', 'skipOnError' => true, 'targetClass' => Node::class, 'targetAttribute' => ['node_id' => 'node_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'access_point_id' => Yii::t('app', 'Access Point ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'strategy_class' => Yii::t('app', 'Strategy Class'),
            'node_id' => Yii::t('app', 'Node ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['node_id' => 'node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIpRanges()
    {
        return $this->hasMany(IpRange::class, ['ap_id' => 'access_point_id']);
    }

    public function getActiveIpRange()
    {
        $range = $this->getIpRanges()
            ->andWhere(['status' => IpRange::AVAILABLE_STATUS])->orderBy(['ip_range_id' => SORT_ASC])
            ->one();

        if (empty($range)) {
            $range = $this->getIpRanges()
            ->andWhere(['status' => IpRange::ENABLED_STATUS])->orderBy(['ip_range_id' => SORT_ASC])
            ->one();

            if (empty($range)) {
                Yii::t('app', 'Access Point {ap} hasn`t ip ranges available', ['ap' => $this->name]);
                return false;
            }
        } 
        
        return $range;
    }

    public function assignIpRange()
    {
        if($this->ranges) {
            foreach($this->ranges as $range) {
                $ipRange = IpRange::findOne($range);

                if ($ipRange) {
                    $ipRange->updateAttributes(['ap_id' => $this->access_point_id]);
                }
            }

            return true;
        }

        return false;
    }

    public function getStatusses() {
        return [
            'enabled' => Yii::t('app', 'Enabled'),
            'disabled' => Yii::t('app', 'Disabled'),
        ];
    }

    public function getStatusLabel() {
        $statusses = $this->statusses;

        return $statusses[$this->status];
    }

    public function getDeletable()
    {
        return !Connection::find()->andWhere(['access_point_id' => $this->access_point_id])->exists() &&
            !IpRange::find()->andWhere(['ap_id' => $this->access_point_id])->exists();
    }
    
}
