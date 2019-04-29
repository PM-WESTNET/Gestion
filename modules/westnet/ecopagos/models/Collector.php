<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;

/**
 * This is the model class for table "collector".
 *
 * @property integer $collector_id
 * @property integer $address_id
 * @property string $name
 * @property string $lastname
 * @property string $number
 * @property string $document_number
 * @property string $document_type
 * @property double $limit
 * @property string $password
 *
 * @property Assignation[] $assignations
 * @property Ecopago[] $ecopagos
 * @property BatchClosure[] $batchClosures
 */
class Collector extends \app\components\db\ActiveRecord {

    const SCENARIO_CREATE = 'create';
    const SCENARIO_VALIDATE_PASSWORD = 'validate_password';

    private $_ecopagos;
    public $hash;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'collector';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbecopago');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'lastname', 'number', 'document_number', 'document_type'], 'required'],
            [['address_id'], 'integer'],
            [['document_type'], 'string'],
            [['limit'], 'number'],
            [['ecopagos'], 'safe'],
            [['name', 'lastname'], 'string', 'max' => 100],
            [['number', 'document_number'], 'string', 'max' => 20],
            ['number', 'unique'],
            [['password'], 'string', 'max' => 255],
            [['password', 'password_repeat'], 'required', 'on' => [Collector::SCENARIO_CREATE, Collector::SCENARIO_VALIDATE_PASSWORD]],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'message' => EcopagosModule::t('app', 'Passwords does not match.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'collector_id' => EcopagosModule::t('app', 'Collector'),
            'address_id' => EcopagosModule::t('app', 'Address'),
            'name' => EcopagosModule::t('app', 'Name'),
            'lastname' => EcopagosModule::t('app', 'Lastname'),
            'number' => EcopagosModule::t('app', 'Number'),
            'document_number' => EcopagosModule::t('app', 'Document number'),
            'document_type' => EcopagosModule::t('app', 'Document type'),
            'limit' => EcopagosModule::t('app', 'Limit'),
            'assignations' => EcopagosModule::t('app', 'Assignations'),
            'ecopagos' => EcopagosModule::t('app', 'Ecopagos'),
            'batchClosures' => EcopagosModule::t('app', 'BatchClosures'),
            'password' => EcopagosModule::t('app', 'Password'),
            'password_repeat' => EcopagosModule::t('app', 'Repeat Password'),
        ];
    }

    /**
     * Returns all available document types
     * @return type
     * TODO pasar a static
     */
    public function fetchDocumentTypes() {
        return [
            'DNI' => EcopagosModule::t('app', 'DNI'),
            'Otro' => EcopagosModule::t('app', 'Other'),
        ];
    }

    /**
     * Returns a simple array with all ecopagos assignated with this collector
     * @return array
     */
    public function fetchEcopagos($asArray = true) {
        $ecopagos = [];

        if (!empty($this->ecopagos)) {

            foreach ($this->ecopagos as $ecopago) {
                $ecopagos[$ecopago->ecopago_id] = $ecopago->name;
            }
        }

        if ($asArray)
            return $ecopagos;
        else
            return implode(', ', $ecopagos);
    }

    /**
     * Returns all collectors with a condition as an array on id => completeName
     * @param type $conditions
     * @return type
     */
    public static function fetchCollectorsAsArray($conditions = []) {

        $collectorArray = [];

        $collectors = Collector::find()->where($conditions)->all();

        if (!empty($collectors)) {
            foreach ($collectors as $collector) {
                $collectorArray[$collector->collector_id] = $collector->getFormattedName();
            }
        }

        return $collectorArray;
    }

    /**
     * Returns a formatted version of this collector name and basic information
     * @return type
     */
    public function getFormattedName() {
        return $this->name . ' ' . $this->lastname . ' (' . $this->number . ')';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignations() {
        return $this->hasMany(Assignation::className(), ['collector_id' => 'collector_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopagos() {
        return $this->hasMany(Ecopago::className(), ['ecopago_id' => 'ecopago_id'])->viaTable('assignation', ['collector_id' => 'collector_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchClosures() {
        return $this->hasMany(BatchClosure::className(), ['collector_id' => 'collector_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: BatchClosures.
     */
    public function getDeletable() {
        if ($this->getBatchClosures()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * Sets a password for this collector
     */
    public function setPassword() {
        if (!empty($this->password) && $this->password == $this->password_repeat) {
            $this->password = $this->password_repeat = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        parent::afterFind();

        //Only sets password on model for validation propouses
        $this->hash = $this->password;
        $this->password = '';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            $this->setPassword();

            //Only for new instances
            if ($insert) {
                
            } else {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a collector is a valid one
     * @return mixed
     */
    public function isValid() {

        //Creates a new collector with an scenario for validating passwords
        $realCollector = new Collector();
        $realCollector->scenario = Collector::SCENARIO_VALIDATE_PASSWORD;

        $realCollector = $realCollector->find()->where([
                    'number' => $this->number,
                ])->one();

        if (!empty($realCollector) && Yii::$app->getSecurity()->validatePassword($this->password, $realCollector->hash))
            return $realCollector;
        else
            return false;
    }

    /**
     * Checks if this collector is from an specific ecopago branch
     * @param type $ecopago_id
     * @return boolean
     */
    public function isFromEcopago($ecopago_id) {

        $ecopago = Ecopago::findOne($ecopago_id);

        $assignation = Assignation::find()->where([
                    'ecopago_id' => $ecopago->ecopago_id,
                    'collector_id' => $this->collector_id,
                ])->one();

        if (!empty($assignation)) {
            return true;
        }

        return false;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Assignations, Ecopagos.
     */
    protected function unlinkWeakRelations() {
        $this->unlinkAll('ecopagos', true);
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

}
