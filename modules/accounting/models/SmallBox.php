<?php

namespace app\modules\accounting\models;

use app\components\db\ActiveRecord;
use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\config\models\Config;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "small_box".
 *
 * @property integer $small_box_id
 * @property string $start_date
 * @property string $close_date
 * @property double $balance
 * @property integer $money_box_account_id
 * @property string $status
 *
 * @property MoneyBoxAccount $moneyBoxAccount
 */
class SmallBox extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'small_box';
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
        return [
            [['start_date', 'money_box_account_id', 'status'], 'required'],
            [['start_date', 'close_date', 'moneyBoxAccount'], 'safe'],
            [['start_date', 'close_date'], 'date'],
            [['balance'], 'number'],
            [['money_box_account_id'], 'integer'],
            [['status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'small_box_id' => 'Small Box ID',
            'start_date' => 'Start Date',
            'close_date' => 'Close Date',
            'balance' => 'Balance',
            'money_box_account_id' => 'Money Box Account ID',
            'status' => 'Status',
            'moneyBoxAccount' => 'MoneyBoxAccount',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMoneyBoxAccount() {
        return $this->hasOne(MoneyBoxAccount::className(), ['money_box_account_id' => 'money_box_account_id']);
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
        $this->start_date = Yii::$app->formatter->asDate($this->start_date);
        $this->close_date = Yii::$app->formatter->asDate($this->close_date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        $this->start_date = Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd');
        $this->close_date = Yii::$app->formatter->asDate($this->close_date, 'yyyy-MM-dd');
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
     * Weak relations: MoneyBoxAccount.
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

    public function close() {
        $movementSearch= new AccountMovementSearch();
        $movementSearch->account_id_from= $this->moneyBoxAccount->account->lft;
        $movementSearch->account_id_to= $this->moneyBoxAccount->account->rgt;
        $movementSearch->fromDate= $this->start_date;
        
        
        
        
        foreach ($this->movements() as $movement) {
            $movement->close();
        }

        $this->status = 'closed';
        $this->save();

        return true;
    }

    public function isOpen() {
        if ($this->status == 'open') {
            return true;
        } else {
            return false;
        }
    }

    public function movements() {
        $movements = [];
        $movementsSeach= new AccountMovementSearch();              
        if ($this->isOpen()) {
            $arrayMovements= $movementsSeach->search(['AccountMovementSearch' =>[
                'fromDate'=> Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd'), 
                'account_id_from' => $this->moneyBoxAccount->account->lft, 
                'account_id_to' => $this->moneyBoxAccount->account->rgt]], 1);
            foreach ($arrayMovements as $movement) {
                $movements[]= $movement;
                
            }
        }else{
            $arrayMovements= $movementsSeach->search(['AccountMovementSearch' =>[
                'fromDate'=> Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd'),
                'toDate' => Yii::$app->formatter->asDate($this->close_date, 'yyyy-MM-dd'),
                'account_id_from' => $this->moneyBoxAccount->account->lft, 
                'account_id_to' => $this->moneyBoxAccount->account->rgt]], 1);
            foreach ($arrayMovements as $movement) {
                    $movements[]= $movement;               
            }
        }
        
        return $movements;
    }
    
    public function lastSmallBoxClosed(){
        $lastSmallBox= self::findOne(['close_date' => '(SELECT MAX(close_date) FROM small_box WHERE money_box_account_id='. $this->money_box_account_id.')', 'status' => 'closed']);
        
        return $lastSmallBox;
    }
    
    public function initBalance(){
        
        $init_balance= (float)Config::getValue('small_box_init_balance');
        $lastBox= $this->lastSmallBoxClosed();
        if ($lastBox !== null) {
            $this->balance= (float)$init_balance + (float)$lastBox->balance;
        }else{
            $this->balance= (float)$init_balance;
        }
        
        
    }
            

}
