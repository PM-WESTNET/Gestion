<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;

/**
 * This is the model class for table "period_closure".
 *
 * @property integer $period_closure_id
 * @property integer $datetime
 * @property integer $cashier_id
 * @property integer $payment_count
 * @property string $first_payout_number
 * @property string $last_payout_number
 * @property string $date
 * @property string $time
 * @property string $date_from
 * @property string $date_to
 * @property string $status
 *
 * @property Payout[] $payouts
 * @property Cashier $cashier
 */
class PeriodClosure extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'period_closure';
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
            [['datetime', 'cashier_id', 'payment_count', 'first_payout_number', 'last_payout_number', 'date', 'time', 'date_from', 'date_to', 'status'], 'required'],
            [['datetime', 'cashier_id', 'payment_count'], 'integer'],
            [['date', 'time', 'date_from', 'date_to', 'cashier'], 'safe'],
            [['date', 'date_from', 'date_to'], 'date'],
            [['status'], 'string'],
            [['first_payout_number', 'last_payout_number'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'period_closure_id' => Yii::t('app', 'Period Closure ID'),
            'datetime' => Yii::t('app', 'Datetime'),
            'cashier_id' => Yii::t('app', 'Cashier ID'),
            'payment_count' => Yii::t('app', 'Payment Count'),
            'first_payout_number' => Yii::t('app', 'First Payout Number'),
            'last_payout_number' => Yii::t('app', 'Last Payout Number'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'date_from' => Yii::t('app', 'Date From'),
            'date_to' => Yii::t('app', 'Date To'),
            'status' => Yii::t('app', 'Status'),
            'payouts' => Yii::t('app', 'Payouts'),
            'cashier' => Yii::t('app', 'Cashier'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayouts() {
        return $this->hasMany(Payout::className(), ['period_closure_id' => 'period_closure_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashier() {
        return $this->hasOne(Cashier::className(), ['cashier_id' => 'cashier_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->date = Yii::$app->formatter->asDate($this->date);
        $this->date_from = Yii::$app->formatter->asDate($this->date_from);
        $this->date_to = Yii::$app->formatter->asDate($this->date_to);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        $this->date_from = Yii::$app->formatter->asDate($this->date_from, 'yyyy-MM-dd');
        $this->date_to = Yii::$app->formatter->asDate($this->date_to, 'yyyy-MM-dd');
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
     * Weak relations: Payouts, Cashier.
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
    
    public function canView($id){
        if ($this->cashier_id == $id) {
            return true;
        }else{
            return false;
        }
    }

}
