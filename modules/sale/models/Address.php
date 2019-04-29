<?php

namespace app\modules\sale\models;

use app\modules\config\models\Config;
use app\modules\zone\models\Zone;
use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $address_id
 * @property string $street
 * @property string $between_street_1
 * @property string $between_street_2
 * @property string $number
 * @property string $block
 * @property string $house
 * @property integer $floor
 * @property string $department
 * @property string $tower
 * @property string $geocode
 * @property integer $zone_id
 * @property string $indications
 *
 * @property Zone $zone
 * @property Customer[] $customers
 */
class Address extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'address';
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
    public function rules() {
        $rules = [
            [['floor', 'zone_id'], 'integer'],
            [['zone',], 'safe'],
            [['street', 'between_street_1', 'between_street_2', 'geocode'], 'string', 'max' => 100],
            [['number', 'block', 'house', 'department', 'tower'], 'string', 'max' => 45],
            [['indications'], 'string'],
            ['geocode', 'geocodeValidation']
        ];

        if(Config::getConfig('customer_address_required')->value) {
            $rules[] = [['zone_id', 'street'], 'required', 'on' => 'insert'];            
        }
        
        return $rules;
        
        
//        if ($this->isNewRecord) {
//            $rules= $this->insertRules();
//        }else{
//            $rules= $this->updateRules();
//        }
//
//        return $rules;
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

    public function insertRules(){
        $rules = [
            [['number', 'floor', 'zone_id'], 'integer'],
            [['zone', ], 'safe'],
            [['street', 'between_street_1', 'between_street_2', 'geocode'], 'string', 'max' => 100],
            [['block', 'house', 'department', 'tower'], 'string', 'max' => 45],
            [['indications'], 'string'],
        ];

        if(Config::getConfig('customer_address_required')->value) {
            $rules[] = [['zone_id', 'street'], 'required'];            
        }
        
        return $rules;
    }
    
    public function updateRules(){
        $rules = [
            [['number', 'floor', 'zone_id'], 'integer'],
            [['zone', ], 'safe'],
            [['street', 'between_street_1', 'between_street_2', 'geocode'], 'string', 'max' => 100],
            [['block', 'house', 'department', 'tower'], 'string', 'max' => 45],
            [['indications'], 'string'],
        ];
        
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'address_id' => Yii::t('app', 'Address ID'),
            'street' => Yii::t('app', 'Street'),
            'between_street_1' => Yii::t('app', 'Between Street 1'),
            'between_street_2' => Yii::t('app', 'Between Street 2'),
            'number' => Yii::t('app', 'Number'),
            'geocode' => Yii::t('app', 'Geo Code'),
            'block' => Yii::t('app', 'Block'),
            'house' => Yii::t('app', 'House'),
            'floor' => Yii::t('app', 'Floor'),
            'department' => Yii::t('app', 'Department'),
            'tower' => Yii::t('app', 'Tower'),
            'zone_id' => Yii::t('app', 'Zone'),
            'zone' => Yii::t('app', 'Zone'),
            'customers' => Yii::t('app', 'Customers'),
            'indications' => Yii::t('app', 'Indications'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone() {
        return $this->hasOne(Zone::className(), ['zone_id' => 'zone_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers() {
        return $this->hasMany(Customer::className(), ['address_id' => 'address_id']);
    }

    /**
     * @brief Returns ordered zone by each parent
     * @param Zone[] $parents
     * @return Zone[]
     * @throws \InvalidArgumentException
     */
    //TODO Borrar -> Creo que no se esta usando.
    public static function getOrderedZone($parents = []) {
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }
        $nested = array();

        if (empty($parents)) {
            $parents = Zone::find()->where("parent_id IS NULL")->all();
        }
        foreach ($parents as $parent) {

            if (empty($parent->zone))
                $nested[] = $parent;
            else {
                $nested[] = $parent;
                $nested = array_merge($nested, self::getOrderedZone($parent->zone));
            }
        }
        return $nested;
    }

    /**
     * @brief Returns indented zone list by its parents
     * @param Zone[] $parents
     * @return Zone[]
     */
    //TODO Borrar -> Creo que no se esta usando.
    public static function getNestedZone($parents = []) {
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }
        $nested = array();
        if (empty($parents)) {
            $parents = Zone::find()->where("parent_id IS NULL")->all();
        }
        foreach ($parents as $parent) {
            if (empty($parent->zone))
                $nested[] = [$parent];
            else {
                $nested[] = [
                    $parent,
                    'children' => array_merge($nested, self::getNestedZone($parent->zone))
                ];
            }
        }

        return $nested;
    }

    /**
     * Returns each model name, indented by its parent
     * @return string $name
     */
    public function getIndentName() {
        $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $parent = $this->parent;
        $name = empty($parent) ? $this->name : $indent . $this->name;
        if (!empty($parent))
            do {
                if ($parent = $parent->parent)
                    $name = $indent . $name;
            }
            while (!empty($parent));

        return $name;
    }

    /**
     * @return string
     * Devuelve un string con la dirección completa.
     */
    public function getFullAddress() {
        $fulladdress = '';
        $zone = $this->zone;
        if ($zone) {
            $zone_aux = $zone->name;
            while (!empty($zone->parent)) {
                $zone_aux = $zone_aux . ', ' . $zone->parent->name;
                $zone = $zone->parent;
            }
        }

        if (!empty($this->street)) {
            $fulladdress = $this->street . ' ' . (empty($this->number) || $this->number === '0'? 'S/N' : $this->number . ',');
        }
        if (!empty($this->between_street_1) && !empty($this->between_street_2)) {
            $fulladdress = $fulladdress . ' entre '. $this->between_street_1 . ' y ' . $this->between_street_2 . ', ' ;
        }
        
        $fulladdress = $fulladdress . ($zone ?  ($this->zone->type === 'zone' ? ' Bº '.$zone_aux : ' ' . $zone_aux ) : '' );
        
        if (!empty($this->block)) {
            $fulladdress = $fulladdress . ', M-' . $this->block;
        }
        if (!empty($this->house)) {
            $fulladdress = $fulladdress . ' C-' . $this->house;
        }
        if (!empty($this->tower)) {
            $fulladdress = $fulladdress . ' T-' . $this->tower;
        }
        if (!empty($this->floor)) {
            $fulladdress = $fulladdress . ' P-' . $this->floor;
        }
        if (!empty($this->department)) {
            $fulladdress = $fulladdress . ' D-' . $this->department;
        }
        if (!empty($this->indications)) {
            $fulladdress = $fulladdress . ' (' . $this->indications . ')';
        }
        return $fulladdress ;
    }

    /**
     * @return string
     * Devuelve un string con la dirrecion reducida
     */
    public function getShortAddress()
    {
        $fulladdress = '';
        $zone = $this->zone;
        if ($zone) {
            $zone_aux = $zone->name;
            while (!empty($zone->parent)) {
                $zone_aux = $zone_aux . ', ' . $zone->parent->name;
                $zone = $zone->parent;
            }
        }

        if (!empty($this->street)) {
            $fulladdress = $this->street . ' ' . (empty($this->number) || $this->number === '0'? 'S/N' : $this->number . ',');
        }
        if (!empty($this->between_street_1) && !empty($this->between_street_2)) {
            $fulladdress = $fulladdress . ' entre '. $this->between_street_1 . ' y ' . $this->between_street_2 . ', ' ;
        }

        $fulladdress = $fulladdress . ($zone ?  ($this->zone->type === 'zone' ? ' Bº '.$zone_aux : ' ' . $zone_aux ) : '' );

        if (!empty($this->block)) {
            $fulladdress = $fulladdress . ', M-' . $this->block;
        }
        if (!empty($this->house)) {
            $fulladdress = $fulladdress . ' C-' . $this->house;
        }
        if (!empty($this->tower)) {
            $fulladdress = $fulladdress . ' T-' . $this->tower;
        }
        if (!empty($this->floor)) {
            $fulladdress = $fulladdress . ' P-' . $this->floor;
        }
        if (!empty($this->department)) {
            $fulladdress = $fulladdress . ' D-' . $this->department;
        }

        return $fulladdress;
    }

    /**
     * @inheritdoc
     * Strong relations: Zone, Customers.
     */
    public function getDeletable() {
        if ($this->getZone()->exists()) {
            return false;
        }
        if ($this->getCustomers()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
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

    /**
     * Devuelve una cadena con la direccion completa
     * @return type
     */
    public function __toString() {
        return $this->fullAddress;
    }

    public function isEqual(Address $address) {
        return (
                $this->street == $address->street &&
                $this->between_street_1 == $address->between_street_1 &&
                $this->between_street_2 == $address->between_street_2 &&
                $this->number == $address->number &&
                $this->block == $address->block &&
                $this->house == $address->house &&
                $this->floor == $address->floor &&
                $this->department == $address->department &&
                $this->tower == $address->tower &&
                $this->zone_id == $address->zone_id &&
                $this->geocode == $address->geocode
                );
    }

    public function afterSave($insert, $changedAttributes) {
        $customer_id = NULL;

        $customer = Customer::findOne(['address_id' => $this->address_id]);
        if (!empty($customer)) {
            $customer_id = $customer->customer_id;
        }

        if ($customer_id === NULL) {
            $contract = \app\modules\sale\modules\contract\models\Contract::findOne(['address_id' => $this->address_id]);
            if (!empty($contract)) {
                $customer_id = $contract->customer_id;
            }
        }

        if ($customer_id !== NULL) {
            if ($insert) {
                $log = new CustomerLog();
                $log->createInsertLog($customer_id, '', $this->address_id);
            } else {
                foreach ($changedAttributes as $attr => $oldValue) {
                    if ($this->$attr != $oldValue) {
                        switch ($attr) {
                            case 'zone_id':
                                $oldZone = Zone::findOne(['zone_id' => $oldValue]);
                                $log = new CustomerLog();
                                $log->createUpdateLog($customer_id, $this->attributeLabels()[$attr], (empty($oldZone) ? '-' : $oldZone->getFullZone($oldZone->zone_id)), $this->zone->getFullZone($this->zone_id), 'Address', $this->address_id);
                                break;
                            default:
                                $log = new CustomerLog();
                                $log->createUpdateLog($customer_id, $this->attributeLabels()[$attr], $oldValue, $this->$attr, 'Address', $this->address_id);
                                break;
                        }
                    }
                }
            }
        }
    }

}
