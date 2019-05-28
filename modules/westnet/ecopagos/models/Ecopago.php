<?php

namespace app\modules\westnet\ecopagos\models;

use app\modules\provider\models\Provider;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;

/**
 * This is the model class for table "ecopago".
 *
 * @property integer $ecopago_id
 * @property integer $address_id
 * @property integer $status_id
 * @property integer $account_id
 * @property integer $create_datetime
 * @property integer $update_datetime
 * @property string $name
 * @property string $description
 * @property double $limit
 * @property string $number
 * @property integer $provider_id
 *
 * @property Account $account
 * @property Assignation[] $assignations
 * @property Collector[] $collectors
 * @property BatchClosure[] $batchClosures
 * @property Cashier[] $cashiers
 * @property Commission[] $commissions
 * @property Commission $activeCommission
 * @property Status $status
 * @property Payout[] $payouts
 * @property Provider $provider
 */
class Ecopago extends \app\components\db\ActiveRecord {

    private $_collectors;
    public $commission_type;
    public $commission_value;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ecopago';
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
            [['status_id', 'name', 'commission_type', 'commission_value', 'account_id', 'provider_id'], 'required'],
            [['address_id', 'status_id', 'create_datetime', 'update_datetime'], 'integer'],
            [['description'], 'string'],
            [['limit'], 'number'],
            [['collectors', 'status'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['number'], 'string', 'max' => 50],
            ['number', 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ecopago_id' => EcopagosModule::t('app', 'Ecopago'),
            'address_id' => EcopagosModule::t('app', 'Address ID'),
            'account_id' => EcopagosModule::t('app', 'Account'),
            'status_id' => EcopagosModule::t('app', 'Status ID'),
            'create_datetime' => EcopagosModule::t('app', 'Create Datetime'),
            'update_datetime' => EcopagosModule::t('app', 'Update Datetime'),
            'name' => EcopagosModule::t('app', 'Name'),
            'description' => EcopagosModule::t('app', 'Description'),
            'limit' => EcopagosModule::t('app', 'Limit'),
            'number' => EcopagosModule::t('app', 'Number'),
            'assignations' => EcopagosModule::t('app', 'Assignations'),
            'collectors' => EcopagosModule::t('app', 'Collectors'),
            'batchClosures' => EcopagosModule::t('app', 'Batch Closures'),
            'cashiers' => EcopagosModule::t('app', 'Cashiers'),
            'commission' => EcopagosModule::t('app', 'Commission'),
            'status' => EcopagosModule::t('app', 'Status'),
            'payouts' => EcopagosModule::t('app', 'Payouts'),
            'commission_type' => EcopagosModule::t('app', 'Commission Type'),
            'commission_value' => EcopagosModule::t('app', 'Commission Value'),
            'provider_id' => Yii::t('app', 'Provider'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount() {
        return $this->hasOne(\app\modules\accounting\models\Account::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignations() {
        return $this->hasMany(Assignation::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCollectors() {
        return $this->hasMany(Collector::className(), ['collector_id' => 'collector_id'])->viaTable('assignation', ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchClosures() {
        return $this->hasMany(BatchClosure::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashiers() {
        return $this->hasMany(Cashier::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommissions() {
        return $this->hasMany(Commission::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveCommission() {
        return $this->hasMany(Commission::className(), ['ecopago_id' => 'ecopago_id'])
                        ->orderBy(['commission_id' => SORT_DESC])
                        ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus() {
        return $this->hasOne(Status::className(), ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayouts() {
        return $this->hasMany(Payout::className(), ['ecopago_id' => 'ecopago_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider() {
        return $this->hasOne(\app\modules\provider\models\Provider::className(), ['provider_id' => 'provider_id']);
    }

    /**
     * Sets Collectors relation on helper variable and handles events insert and update
     */
    public function setCollectors($collectors) {

        if (empty($collectors)) {
            $collectors = [];
        }

        $this->_collectors = $collectors;

        $saveCollectors = function($event) {
            $this->unlinkAll('collectors', true);

            foreach ($this->_collectors as $id) {
                $this->link('collectors', Collector::findOne($id), [
                    'datetime' => time(),
                    'date' => date('Y-m-d'),
                    'time' => date('H:i')
                ]);
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $saveCollectors);
        $this->on(self::EVENT_AFTER_UPDATE, $saveCollectors);
    }

    /**
     * Returns a simple array with all collectors assignated to this Ecopago
     * @return array
     */
    public function fetchCollectors($asArray = true) {
        $collectors = [];

        if (!empty($this->collectors)) {

            foreach ($this->collectors as $collector) {
                $collectors[$collector->collector_id] = $collector->getFormattedName();
            }
        }

        if ($asArray)
            return $collectors;
        else
            return implode(', ', $collectors);
    }

    /**
     * Returns a simple array with all cashiers assignated to this Ecopago
     * @return array
     */
    public function fetchCashiers($asArray = true) {
        $cashiers = [];

        if (!empty($this->cashiers)) {

            foreach ($this->cashiers as $cashier) {
                $cashiers[$cashier->cashier_id] = $cashier->name . ' ' . $cashier->lastname;
            }
        }

        if ($asArray)
            return $cashiers;
        else
            return implode(', ', $cashiers);
    }

    /**
     * Creates a commission for this Ecopago branch
     */
    public function createCommission() {
        $commission = new Commission;
        $commission->type = $this->commission_type;
        $commission->value = $this->commission_value;
        $commission->create_datetime = time();
        $commission->ecopago_id = $this->ecopago_id;
        $commission->save();
    }

    /**
     * Checks if this ecopago branch is near its payout limit or not
     */
    public function isNearLimit() {

        $payoutsAmount = $this->fetchValidPayouts();

        if (!empty($payoutsAmount)) {

            $limit = $this->limit;
            $currentAmount = $payoutsAmount;
            $difference = $limit - $currentAmount;

            $percentage = 100 - (($difference * 100) / $limit);

            if ($percentage >= 85)
                return true;

            return false;
        } else
            return false;
    }

    /**
     * Checks if this ecopago branch is below payout limit or not
     * @return boolean
     */
    public function isOnLimit() {

        $payoutsAmount = $this->fetchValidPayouts();

        if (empty($payoutsAmount) || $payoutsAmount <= $this->limit) {
            return true;
        } else
            return false;
    }

    /**
     * Returns all valid payouts not yet closed by a batch closure
     * @return type
     */
    private function fetchValidPayouts() {

        //$lastSunday = date('d/m/Y', strtotime('last Sunday', strtotime(date('Y-m-d'))));
        //$nextSunday = date('d/m/Y', strtotime('next Sunday', strtotime(date('Y-m-d'))));
        return Payout::find()->where([
                            'ecopago_id' => $this->ecopago_id,
                        ])
                        ->andWhere(['<>', 'status', Payout::STATUS_CLOSED_BY_BATCH])
                        ->andWhere(['<>', 'status', Payout::STATUS_REVERSED])
                        //->andWhere(['>=', 'date', $lastSunday])
                        //->andWhere(['<', 'date', $nextSunday])
                        ->sum('amount');
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            //Only for new instances
            if ($insert) {
                $this->create_datetime = time();
            } else {
                $this->update_datetime = time();
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

        if (!empty($this->activeCommission)) {
            $this->commission_type = $this->activeCommission->type;
            $this->commission_value = $this->activeCommission->value;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        $this->createCommission();

        if ($insert) {
            
        } else {
            
        }
    }

    /**
     * @inheritdoc
     * Strong relations: Cashiers.
     */
    public function getDeletable() {
        if ($this->getCashiers()->exists()) {
            return false;
        }
        if ($this->getBatchClosures()->exists()) {
            return false;
        }
        if ($this->getPayouts()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Assignations, Collectors, BatchClosures, Commissions, Status, Payouts.
     */
    protected function unlinkWeakRelations() {
        $this->unlinkAll('collectors', true);
        $this->unlinkAll('commissions', true);
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

    public function canDisable() {

        $enabled_status = Status::findOne(['slug' => 'enabled']);

        return $this->status_id === $enabled_status->status_id && !Payout::find()
            ->andWhere(['ecopago_id' => $this->ecopago_id])
            ->andWhere(['IN', 'status', ['valid', 'closed']])
            ->exists() && !DailyClosure::find()->andWhere([
                'ecopago_id' => $this->ecopago_id,
                'status' => 'open'
            ])->exists();
    }

    /**
     * Deshabilita por completo el ecopago. Se les quita el acceso a los cajeros
     */
    public function disable()
    {
        if ($this->canDisable()){

            $cashiers = $this->cashiers;
            $transaction = Yii::$app->dbecopago->beginTransaction();
            foreach ($cashiers as $cashier) {
                $user = User::findOne($cashier->user_id);

                if ($user) {
                    $user->updateAttributes(['status' => User::STATUS_BANNED, 'updated_at' => time()]);
                }

                $cashier->updateAttributes(['status' => 'inactive']);
            }

            $status = Status::findOne(['slug'  => 'disabled']);

            if (empty($status)){
                $transaction->rollBack();
                return false;
            }

            $this->status_id = $status->status_id;
            if($this->updateAttributes(['status_id', 'update_datetime' => time()])) {
                $transaction->commit();
                return true;
            }
            $transaction->rollBack();
        }

        return false;

    }
}
