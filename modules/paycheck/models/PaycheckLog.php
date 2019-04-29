<?php

namespace app\modules\paycheck\models;

use app\modules\accounting\models\MoneyBoxAccount;
use Yii;

/**
 * This is the model class for table "paycheck_log".
 *
 * @property integer $paycheck_log_id
 * @property integer $paycheck_id
 * @property integer $timestamp
 * @property string $status
 * @property string $description
 * @property integer $money_box_account_id
 *
 * @property Paycheck $paycheck
 * @property MoneyBoxAccount $moneyBoxAccount
 */
class PaycheckLog extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paycheck_log';
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
            [['paycheck_id'], 'required'],
            [['paycheck_log_id', 'paycheck_id', 'timestamp', 'money_box_account_id'], 'integer'],
            [['paycheck', 'timestamp'], 'safe'],
            [['status'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 255]

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'paycheck_log_id' => Yii::t('paycheck', 'Paycheck Log ID'),
            'paycheck_id' => Yii::t('paycheck', 'Paycheck ID'),
            'timestamp' => Yii::t('paycheck', 'Date'),
            'status' => Yii::t('paycheck', 'Status'),
            'paycheck' => Yii::t('paycheck', 'Paycheck'),
            'description' => Yii::t('paycheck', 'Description'),
            'money_box_account_id' => Yii::t('app', 'Money Box Acount'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaycheck()
    {
        return $this->hasOne(Paycheck::className(), ['paycheck_id' => 'paycheck_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::className(), ['money_box_account_id' => 'money_box_account_id']);
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
     * Weak relations: Paycheck.
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

}
