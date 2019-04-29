<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;

/**
 * This is the model class for table "commission".
 *
 * @property integer $commission_id
 * @property integer $ecopago_id
 * @property integer $create_datetime
 * @property integer $update_datetime
 * @property string $type
 * @property double $value
 *
 * @property Ecopago $ecopago
 */
class Commission extends \app\components\db\ActiveRecord {

    const COMMISSION_TYPE_FIXED = 'fixed';
    const COMMISSION_TYPE_PERCENTAGE = 'percentage';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'commission';
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
            [['ecopago_id', 'create_datetime'], 'required'],
            [['ecopago_id', 'create_datetime', 'update_datetime'], 'integer'],
            [['type'], 'string'],
            [['value'], 'number'],
            [['ecopago'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'commission_id' => EcopagosModule::t('app', 'Commision'),
            'ecopago_id' => EcopagosModule::t('app', 'Ecopago'),
            'create_datetime' => EcopagosModule::t('app', 'Create Datetime'),
            'update_datetime' => EcopagosModule::t('app', 'Update Datetime'),
            'type' => EcopagosModule::t('app', 'Type'),
            'value' => EcopagosModule::t('app', 'Value'),
            'ecopago' => EcopagosModule::t('app', 'Ecopago'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopago() {
        return $this->hasOne(Ecopago::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * Returns all available commission types
     * @return type
     */
    public static function fetchCommissionTypes() {
        return [
            self::COMMISSION_TYPE_PERCENTAGE => EcopagosModule::t('app', 'Percentage'),
            self::COMMISSION_TYPE_FIXED => EcopagosModule::t('app', 'Fixed'),
        ];
    }

    /**
     * Returns symbol for this commission depending on type
     * @return string
     */
    public function fetchSymbol() {
        switch ($this->type) {
            case self::COMMISSION_TYPE_FIXED : 
                return '$';
                break;
            case self::COMMISSION_TYPE_PERCENTAGE : 
                return '%';
                break;
        }
    }

    /**
     * @inheritdoc
     * Strong relations: Ecopago.
     */
    public function getDeletable() {
        if ($this->getEcopago()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
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

}
