<?php

namespace app\modules\zone\models;

use app\modules\sale\models\Address;
use Yii;

/**
 * This is the model class for table "zone".
 *
 * @property integer $zone_id
 * @property string $name
 * @property integer $parent_id
 * @property integer $postal_code
 * @property integer $create_timestamp
 * @property integer $update_timestamp
 * @property string $status
 * @property string $type
 * @property integer $lft
 * @property integer $rgt
 *
 * @property Zone $parent
 * @property Zone[] $zones
 */
class Zone extends \app\components\db\ActiveRecord
{

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zone';
    }
    
    public function behaviors()
    {
        return [
            'slug'=>[
                'class' => \yii\behaviors\SluggableBehavior::className(),
                'slugAttribute' => 'system',
                'attribute' => 'name',
                'ensureUnique'=>true
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp'],
                    \yii\db\ActiveRecord::EVENT_AFTER_UPDATE => ['update_timestamp'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'status'], 'required'],
            [['parent_id', 'create_timestamp', 'update_timestamp', 'postal_code', 'lft', 'rgt'], 'integer'],
            [['status','type'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'zone_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'parent_id' => Yii::t('app', 'Parent'),
            'create_timestamp' => Yii::t('app', 'Create Timestamp'),
            'update_timestamp' => Yii::t('app', 'Update Timestamp'),
            'status' => Yii::t('app', 'Status'),
            'parent' => Yii::t('app', 'Parent'),
            'postal_code' => Yii::t('app', 'Postal Code'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Zone::className(), ['zone_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZones()
    {
        return $this->hasMany(Zone::className(), ['parent_id' => 'zone_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasMany(Address::class, ['zone_id' => 'zone_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if($this->getZones()->exists()){
            return false;
        }

        if($this->getAddress()->exists()) {
            return false;
        }
        
        return true;
        
    }
    
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Parent, Zones.
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
     * @return string
     * Devuelve el nombre de la zona identado
     */
    public function getTabName()
    {
        //Establece una sangría por nivel de Padres de Categoria
        $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';

        // igualo $parent al padre
        $parent = $this->parent;
        // si $parent esta vació es decir no tiene padre el name queda igual SINO le agrega una sangría con $indent
        $name = empty($parent) ? $this->name : $indent . $this->name;

        if (!empty($parent)){
            do {
                if ($parent = $parent->parent)
                    $name = $indent . $name; //le agrego otra sangría
            }
            while (!empty($parent)); // mientras tenga padre
        }

        return $name;
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        // Si tiene padre busco el ultimo hijo para sacar rgt
        if($this->parent) {
            $lastChild = $this->parent->getZones()->orderBy(['zone_id'=>SORT_DESC])->one();
            $rgt = ($lastChild ? $lastChild->rgt : $this->parent->lft );
            // Actualizo todos los nodos
            Yii::$app->db->createCommand('UPDATE zone SET rgt = rgt+2 WHERE rgt > ' . $rgt)->execute();
            Yii::$app->db->createCommand('UPDATE zone SET lft = lft+2 WHERE lft > ' . $rgt)->execute();
            $this->lft = $rgt +1;
            $this->rgt = $rgt +2;
        } else {
            // Si no hay padre busco algun hermano
            $sibling = Zone::find()->where('parent_id is null')->orderBy(['zone_id'=>SORT_DESC])->one();
            $rgt = ($sibling ? $sibling->rgt : 0 );
            $this->lft = $rgt +1;
            $this->rgt = $rgt +2;
        }
        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $width = $this->rgt - $this->lft + 1;
        // Borro todos los hijos.
        Yii::$app->db->createCommand('DELETE FROM zone WHERE lft BETWEEN ' . $this->lft . " AND " . $this->rgt)->execute();

        Yii::$app->db->createCommand('UPDATE zone SET rgt = rgt - ' . $width . " WHERE rgt > " . $this->rgt)->execute();
        Yii::$app->db->createCommand('UPDATE zone SET lft = lft - ' . $width . " WHERE lft > " . $this->rgt)->execute();
    }


    /**
     * @param $zone_id
     * @return string
     * Devuelve un string con el arbol de padres de una zona
     */
    public function getFullZone($zone_id)
    {
        $zone=  Zone::findOne($zone_id);
        $zone_aux='';
        if (!empty($zone)) {
            $zone_zone=true;
            $zone_aux= $zone->name;
            if($zone->type!= 'zone'){
                $zone_zone=false;
                $zone_aux = $zone->name;
            }
            while (!empty($zone->parent)) {
                $zone_aux = $zone_aux . ', ' . $zone->parent->name;
                $zone = $zone->parent;
            }
        }
        if(!$zone_zone){
            $zone_aux=$zone_aux;
        }
        return $zone_aux;
    }

    /**
     * Retorna las zonas para ser listadas en los selects
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getForSelect() {
        return Zone::find()
            ->select(["node.zone_id", "node.parent_id", "node.name", "CONCAT( REPEAT('&nbsp;&nbsp;', COUNT(parent.name) - 1), node.name ) as name", "(COUNT(parent.name) - 1) AS level", "node.lft", "node.rgt"])
            ->from(['zone AS node'])
            ->innerJoin(['zone AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt' )
            ->groupBy(['node.name', 'node.parent_id'])
            ->orderBy('node.lft')->all();
    }
    
    public static function searchByName($name) {
        $query=Zone::find();
        
        $query->filterWhere(['like', 'name', $name]);
        $query->andFilterWhere(['status'=> self::STATUS_ENABLED]);
        
        return $query->all();
            

    }

    /**
     * @param $zone_id
     * @return string
     * Devuelve un string con el arbol de padres de una zona
     */
    public function getFullZoneAPI($zone_id)
    {
        $zone =  Zone::findOne($zone_id);
        $full_zone=[];
        if (!empty($zone)) {
            $zone_zone=true;
            $zone_aux= $zone->name;
            if($zone->type!= 'zone'){
                $zone_zone=false;
                $zone_aux = $zone->name;
            }

            while (!empty($zone->parent)) {
                $full_zone[$zone->parent->type] = $zone->parent->name;
                $zone = $zone->parent;
            }
        }

        return $full_zone;
    }
}