<?php

namespace app\modules\paycheck\models;

use app\modules\accounting\models\MoneyBoxAccount;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "checkbook".
 *
 * @property integer $checkbook_id
 * @property integer $start_number
 * @property integer $end_number
 * @property integer $enabled
 * @property integer $money_box_account_id
 * @property integer $last_used
 *
 * @property MoneyBoxAccount $moneyBoxAccount
 * @property Paycheck[] $paychecks
 */
class Checkbook extends \app\components\db\ActiveRecord
{
    public $money_box_id;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkbook';
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
            [['start_number', 'end_number', 'money_box_account_id'], 'required'],
            [['start_number', 'end_number', 'enabled', 'money_box_account_id', 'last_used'], 'integer'],
            [['last_used'], 'default', 'value'=>0],
            [['moneyBoxAccount', 'money_box_id'], 'safe'],
            ['start_number', 'validateNumbers']
        ];
    }

    public function validateNumbers($attribute, $params)
    {
        $query = Checkbook::find()
            ->where($this->start_number .' BETWEEN start_number and end_number')
            ->andFilterWhere(['money_box_account_id'=>$this->money_box_account_id]);
        if(!$this->isNewRecord){
            $query->andFilterWhere(['<>', 'checkbook_id', $this->checkbook_id]);
        }
        if($query->count() > 0 ) {
            $this->addError($attribute, Yii::t('paycheck', 'There is already a checkbook with the range selected'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money_box_id' => Yii::t('paycheck', 'Money Box'),
            'money_box_account_id' => Yii::t('paycheck', 'Money Box Account'),
            'checkbook_id' => Yii::t('paycheck', 'Checkbook ID'),
            'start_number' => Yii::t('paycheck', 'Start Number'),
            'end_number' => Yii::t('paycheck', 'End Number'),
            'last_used' => Yii::t('paycheck', 'Last Used'),
            'enabled' => Yii::t('paycheck', 'Enabled'),
            'moneyBoxAccount' => Yii::t('paycheck', 'MoneyBoxAccount'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::class, ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaychecks()
    {
        return $this->hasMany(Paycheck::class, ['checkbook_id' => 'checkbook_id']);
    }
    
        
    public function afterFind()
    {
        if($this->money_box_account_id) {
            $this->money_box_id = $this->moneyBoxAccount->money_box_id;
        }
    }
                 
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if ($this->getPaychecks()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MoneyBoxAccount, Paychecks.
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

    public static function findActive($money_box_account_id)
    {
        $query = new Query();
        $query->select('*')
            ->from('checkbook')
            ->where(['money_box_account_id' => $money_box_account_id])
            ->andWhere(['=', 'enabled', true ])
            ->andWhere('end_number > last_used');

        return $query;
    }

    public function getLastNumberUsed()
    {
        if ($this->last_used == 0 && $this->start_number != 0) {
            $this->last_used = $this->start_number;
        }
        return $this->last_used;
    }

    public function getName()
    {
        return Yii::t('app', 'From') . ': ' . $this->start_number . ' - '
            . Yii::t('app', 'To') . ': ' . $this->end_number;
    }

}
