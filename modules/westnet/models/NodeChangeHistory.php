<?php

namespace app\modules\westnet\models;

use app\components\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "node_change_history".
 *
 * @property int $node_change_history_id
 * @property int $node_change_process_id
 * @property int $old_node_id
 * @property int $connection_id
 * @property int $old_ip
 * @property int $new_ip
 * @property string $created_at
 *
 * @property Connection $connection
 * @property Node $newNode
 * @property NodeChangeProcess $nodeChangeProcess
 */
class NodeChangeHistory extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'node_change_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_node_id', 'connection_id', 'old_ip', 'new_ip', 'created_at', 'node_change_process_id'], 'required'],
            [['node_change_process_id', 'old_node_id', 'connection_id', 'old_ip', 'new_ip'], 'integer'],
            [['created_at'], 'safe'],
            [['connection_id'], 'exist', 'skipOnError' => true, 'targetClass' => Connection::class, 'targetAttribute' => ['connection_id' => 'connection_id']],
            [['old_node_id'], 'exist', 'skipOnError' => true, 'targetClass' => Node::class, 'targetAttribute' => ['old_node_id' => 'node_id']],
            [['node_change_process_id'], 'exist', 'skipOnError' => true, 'targetClass' => NodeChangeProcess::class, 'targetAttribute' => ['node_change_process_id' => 'node_change_process_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'node_change_history_id' => Yii::t('app', 'Node Change History'),
            'node_change_process_id' => Yii::t('app', 'Node Change Process'),
            'old_node_id' => Yii::t('app', 'Old Node'),
            'connection_id' => Yii::t('app', 'Connection'),
            'old_ip' => Yii::t('app', 'Old Ip'),
            'new_ip' => Yii::t('app', 'New Ip'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function behaviors()
    {
        return [
            'created_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => function(){
                    return (new \DateTime('now'))->format('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnection()
    {
        return $this->hasOne(Connection::class, ['connection_id' => 'connection_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOldNode()
    {
        return $this->hasOne(Node::class, ['node_id' => 'old_node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodeChangeProcess()
    {
        return $this->hasOne(NodeChangeProcess::class, ['node_change_process_id' => 'node_change_process_id']);
    }
}
