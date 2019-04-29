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
 *
 * @property Node $node
 */
class IpRange extends \app\components\db\ActiveRecord
{


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
            [['node_id', 'ip_start', 'ip_end'], 'integer'],
            [['node_id', 'ip_start', 'ip_end'], 'required'],
            [['node'], 'safe'],
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


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::className(), ['node_id' => 'node_id']);
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

}
