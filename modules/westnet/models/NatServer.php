<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "server".
 *
 * @property integer $nat_server_id
 * @property string $description
 * @property integer $status
 * @property date $create_at
 * @property date $updated_at
 *
 * @property Node[] $nodes
 */
class NatServer extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nat_server';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['status', 'create_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nat_server_id' => Yii::t('westnet', 'Nat Server ID'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('westnet', 'Fecha de Creación'),
            'updated_at' => Yii::t('westnet', 'Fecha de Actualización'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Node::className(), ['nat_server_id' => 'nat_server_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->getNodes()->count() ==0);
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Nodes.
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


    /**
    * Return all nat servers
    */
    public static function findNatServerAll(){
        return self::find()->where(['status' => 1])->all();
    }
}
