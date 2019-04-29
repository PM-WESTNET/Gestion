<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;

/**
 * This is the model class for table "batch_closure_has_payout".
 *
 * @property integer $batch_closure_id
 * @property integer $payout_id
 *
 * @property BatchClosure $batchClosure
 * @property Payout $payout
 */
class BatchClosureHasPayout extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'batch_closure_has_payout';
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
            [['batch_closure_id', 'payout_id'], 'required'],
            [['batch_closure_id', 'payout_id'], 'integer'],
            [['batchClosure', 'payout'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'batch_closure_id' => EcopagosModule::t('app', 'Batch Closure ID'),
            'payout_id' => EcopagosModule::t('app', 'Payout ID'),
            'batchClosure' => EcopagosModule::t('app', 'BatchClosure'),
            'payout' => EcopagosModule::t('app', 'Payout'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchClosure() {
        return $this->hasOne(BatchClosure::className(), ['batch_closure_id' => 'batch_closure_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayout() {
        return $this->hasOne(Payout::className(), ['payout_id' => 'payout_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: BatchClosure, Payout.
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
