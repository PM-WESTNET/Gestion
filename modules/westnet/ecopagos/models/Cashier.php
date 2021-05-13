<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;

/**
 * This is the model class for table "cashier".
 *
 * @property integer $cashier_id
 * @property integer $address_id
 * @property integer $ecopago_id
 * @property integer $user_id
 * @property string $username
 * @property string $name
 * @property string $lastname
 * @property string $number
 * @property string $document_number
 * @property string $document_type
 * @property string $status
 *
 * @property Ecopago $ecopago
 * @property Payout[] $payouts
 * @property User $user
 */
class Cashier extends \app\components\db\ActiveRecord {

    //Scenarios
    const SCENARIO_CREATE = 'create';
    const SCENARIO_CHANGE_PASSWORD = 'change_password';
    //Roles
    const ROLE_CASHIER = 'cashier';
    //Status
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public $userModelClass;
    public $userModelId;
    public $password;
    public $password_repeat;

    public function init() {

        parent::init();

        $ecopagosModule = Yii::$app->getModule('isp')->getModule('ecopagos');

        if (isset($ecopagosModule->params['user']['class']))
            $this->userModelClass = $ecopagosModule->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset($ecopagosModule->params['user']['idAttribute']))
            $this->userModelId = $ecopagosModule->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'cashier';
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
            [['ecopago_id', 'name', 'lastname', 'number', 'document_number', 'document_type', 'username', 'status'], 'required'],
            [['address_id', 'ecopago_id', 'user_id'], 'integer'],
            [['document_type', 'status'], 'string'],
            [['ecopago'], 'safe'],
            [['name', 'lastname'], 'string', 'max' => 100],
            [['number', 'document_number'], 'string', 'max' => 20],
            [['username'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 255],
            [['password', 'password_repeat'], 'required', 'on' => [static::SCENARIO_CREATE, static::SCENARIO_CHANGE_PASSWORD]],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'message' => EcopagosModule::t('app', 'Passwords does not match.')],
            [['number', 'username'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'cashier_id' => EcopagosModule::t('app', 'Cashier'),
            'address_id' => EcopagosModule::t('app', 'Address'),
            'ecopago_id' => EcopagosModule::t('app', 'Ecopago'),
            'name' => EcopagosModule::t('app', 'Name'),
            'lastname' => EcopagosModule::t('app', 'Lastname'),
            'number' => EcopagosModule::t('app', 'Number'),
            'document_number' => EcopagosModule::t('app', 'Document number'),
            'document_type' => EcopagosModule::t('app', 'Document type'),
            'username' => EcopagosModule::t('app', 'Username'),
            'password' => EcopagosModule::t('app', 'Password'),
            'password_repeat' => EcopagosModule::t('app', 'Repeat Password'),
            'ecopago' => EcopagosModule::t('app', 'Ecopago'),
            'payouts' => EcopagosModule::t('app', 'Payouts'),
            'status' => EcopagosModule::t('app', 'Status'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public function fetchStatuses() {
        return [
            static::STATUS_ACTIVE => EcopagosModule::t('app', 'Active'),
            static::STATUS_INACTIVE => EcopagosModule::t('app', 'Inactive'),
        ];
    }

    /**
     * Returns all available statuses
     * @return type
     */
    public static function staticFetchStatuses() {
        return [
            static::STATUS_ACTIVE => EcopagosModule::t('app', 'Active'),
            static::STATUS_INACTIVE => EcopagosModule::t('app', 'Inactive'),
        ];
    }

    /**
     * Returns all available document types
     * @return type
     */
    public function fetchDocumentTypes() {
        return [
            'DNI' => EcopagosModule::t('app', 'DNI'),
            'Otro' => EcopagosModule::t('app', 'Other'),
        ];
    }

    /**
     * Returns all cashiers from an ecopago branch
     * @return Cashier[]
     */
    public static function fetchCashiersFromEcopago($ecopago_id) {
        return static::find()
                        ->orderBy(['name' => SORT_ASC])
                        ->where(['ecopago_id' => $ecopago_id])
                        ->all();
    }

    /**
     * Returns the complete name of this cashier
     * @return string
     */
    public function getCompleteName() {
        return $this->name . ' ' . $this->lastname;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopago() {
        return $this->hasOne(Ecopago::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;
        return $this->hasOne($userModel::className(), [$userPK => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayouts() {
        return $this->hasMany(Payout::className(), ['cashier_id' => 'cashier_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: Payouts.
     */
    public function getDeletable() {
        if ($this->getPayouts()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Ecopago.
     */
    protected function unlinkWeakRelations() {
        $this->deleteSystemUser();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            //Only for new instances
            if ($insert) {
                //Set this cashier's user
                $this->createSystemUser();
            } else {
                $this->updateSystemUser();
            }

            //if there are any errors, we cannot save this cashier
            if ($this->hasErrors()) {
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        parent::afterFind();
        if (!empty($this->user->username))
            $this->username = $this->user->username;
        else
            $this->username = $this->user;
    }

    /**
     * Creates a user for this cashier (login and other stuff)
     */
    private function createSystemUser() {

        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;

        $user = new $userModel();
        $user->status = $userModel::STATUS_ACTIVE;
        $user->username = $this->username;
        $user->password = $this->password;
        $user->save();

        if ($user::assignRole($user->$userPK, Cashier::ROLE_CASHIER)) {
            $this->user_id = $user->$userPK;
        } else {
            $user->delete();
            $this->addError('user_id', EcopagosModule::t('app', 'Please, execute migrations for Ecopago module (westnet/ecopagos/migrations)'));
        }
    }

    /**
     * Updates the user for this cashier
     */
    private function updateSystemUser() {

        $user = $this->user;
        $user->username = $this->username;

        if (!empty($this->password))
            $user->password = $this->password;

        $user->save(false);
    }

    /**
     * Deletes this cashier's system user
     */
    private function deleteSystemUser() {
        $user = $this->user;
        if (!empty($user))
            $user->delete();
    }

    /**
     * Disables this cashier  
     * @return boolean
     */
    public function disable() {

        $userModel = $this->userModelClass;

        if ($this->status != static::STATUS_INACTIVE) {
            $this->status = static::STATUS_INACTIVE;
            $this->user->status = $userModel::STATUS_INACTIVE;
            $this->user->save();
            return true;
        } else
            return false;
    }

    /**
     * Checks whether this cashier is active or not
     * @return boolean
     */
    public function isActive() {
        if ($this->status == static::STATUS_ACTIVE)
            return true;
        else
            return false;
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
     * @return DailyClosure|bool|null
     * Devuelve el cierre de lote actual.
     */
    public function currentDailyClosure() {
        $dailyClosure = DailyClosure::findOne(['cashier_id'=> $this->cashier_id, 'status'=> 'open']);

        if($dailyClosure) {
            return $dailyClosure;
        }

        return false;
        
    }

}
