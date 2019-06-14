<?php

namespace app\modules\accounting\models;

use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\paycheck\models\Checkbook;
use app\modules\sale\models\Currency;
use Yii;

/**
 * This is the model class for table "money_box_account".
 *
 * @property integer $money_box_account_id
 * @property string $number
 * @property integer $enable
 * @property integer $money_box_id
 * @property integer $currency_id
 * @property integer $small_box
 * @property string  $type 
 *
 * @property Checkbook[] $checkbooks
 * @property MoneyBox $moneyBox
 * @property Account $account
 * @property Currency $currency
 */
class MoneyBoxAccount extends \app\components\companies\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_box_account';
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
        $rules = [
            [['number', 'money_box_id', 'currency_id'], 'required'],
            [['enable', 'money_box_id', 'account_id', 'small_box'], 'integer'],
            [['moneyBox', 'account', 'company_id',  'type'], 'safe'],
            [['number'], 'string', 'max' => 45],
            ['account_id', 'required', 'when' =>
                function($model) {
                    return (boolean)$model->small_box;
                },
                //TODO Que lindo js en el model!!
                'whenClient' => "function (attribute, value) {
                    return $('#moneyboxaccount-small_box').is(':checked');
                }"
            ]
        ];

        if (Yii::$app->params['companies']['enabled']) {
            $rules[] = [['company_id'], 'required'];
        }


        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money_box_account_id' => Yii::t('accounting', 'ID'),
            'number' => Yii::t('app', 'Number'),
            'enable' => Yii::t('app', 'Active'),
            'money_box_id' => Yii::t('accounting', 'Money Box'),
            'Account Movements' => Yii::t('accounting', 'Account Movements'),
            'Money Box' => Yii::t('accounting', 'Money Box'),
            'account_id' => Yii::t('accounting', 'Account'),
            'Account' => Yii::t('accounting', 'Account'),
            'Checkbooks' => Yii::t('accounting', 'Checkbooks'),
            'currency_id' => Yii::t('app', 'Currency'),
            'small_box' => Yii::t('app', 'Small Box?'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckbooks()
    {
        return $this->hasMany(Checkbook::class, ['money_box_account_id' => 'money_box_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBox()
    {
        return $this->hasOne(MoneyBox::class, ['money_box_id' => 'money_box_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['currency_id' => 'currency_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return !($this->getCheckbooks()->count() > 0) ;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: AccountMovements, MoneyBox.
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
            return false;
        } else {
            return false;
        }
    }
    
    public static function findDailyBoxes()
    {
        return self::find()->where(['type' => 'daily'])->all();
    }
    
    /**
     * Cierra la caja chica para el dia solicitado
     * @param date $date
     * @return boolean
     * @throws \yii\web\HttpException
     */
    public function closeDailyBox($date)
    {
        $date = Yii::$app->formatter->asDate($date, 'yyyy-MM-dd');

        if(!$this->small_box){
            return false;
        }
        
        //Hay cajas anteriores no cerradas?
        $pending = $this->dailyBoxPendingClose($date);
        if($pending){

            $body = Yii::t('accounting', 'Daily box from {date} has not been closed.', ['date' => $pending]).' ';
            $body .= Html::a('<strong>'.Yii::t('accounting', 'Click here to close.').'</strong>', ['/accounting/money-box-account/close-small-box', 'id' => $this->money_box_account_id, 'date' => $pending]);

            throw new \yii\web\HttpException(405, $body);
        }
        
        $account = $this->account;
        
        $items = $this->account->getAccountMovementItems()->joinWith(['accountMovement' => function($query) use($date){ return $query->andWhere(['date' => $date]); }])->where(['account_movement_item.status' => 'draft'])->all();
        foreach($items as $item){
            $status = $item->accountMovement->close();
            
            if($status != true){
                throw new \yii\web\HttpException(500, Yii::t('app', 'Can\'t close movement {movement}', ['movement' => $item->account_movement_id]));
            }
        }
        
        $this->updateAttributes([
            'daily_box_last_closing_date' => $date,
            'daily_box_last_closing_time' => date('H:i:s')
        ]);
        
        return true;
        
    }
    
    /**
     * Cajas chicas sin cerrar previas a la fecha $date?
     * @return boolean
     */
    public function dailyBoxPendingClose($date = null)
    {
        if(empty($date)){
            $date = date('Y-m-d');
        }else{
            $date = Yii::$app->formatter->asDate($date, 'yyyy-MM-dd');
        }
        
        $pending = AccountMovement::find()->where(['daily_money_box_account_id' => $this->money_box_account_id, 'status' => AccountMovement::STATE_DRAFT])->andWhere('date<"'.$date.'"')->one();
        
        if($pending){
            return $pending->date;
        }
        
        return false;
    }
    
    /**
     * Caja chica sin cerrar en la fecha $date?
     * @return boolean
     */
    public function isDailyBoxClosed($date)
    {
        $date = Yii::$app->formatter->asDate($date, 'yyyy-MM-dd');
        
        $movs = AccountMovement::find()->where(['daily_money_box_account_id' => $this->money_box_account_id])->andWhere('date="'.$date.'"')->exists();
        
        if($movs){
            return !AccountMovement::find()->where(['daily_money_box_account_id' => $this->money_box_account_id, 'status' => AccountMovement::STATE_DRAFT])->andWhere('date="'.$date.'"')->exists();
        }
        
        return false;
    }
    
}
