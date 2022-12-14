<?php

namespace app\modules\accounting\models;

use app\components\workflow\WithWorkflow;
use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\partner\models\PartnerDistributionModel;
use Codeception\Util\Debug;
use Yii;

/**
 * This is the model class for table "account_movement".
 *
 * @property integer $account_movement_id
 * @property string $description
 * @property string $status
 * @property string $date
 * @property string $time
 * @property integer $accounting_period_id
 * @property integer $partner_distribution_model_id
 * @property integer $small_money_box_account_id
 * @property integer $check
 * 
 *
 * @property AccountMovementItem[] $accountMovementItems
 * @property AccountingPeriod $accountingPeriod
 * @property PartnerDistributionModel $partnerDistributionModel
 * @property MoneyBoxAccount $dailyMoneyBoxAccount
 * @property MoneyBoxAccount $smallMoneyBoxAccount
 */
class AccountMovement extends \app\components\companies\ActiveRecord
{
    use WithWorkflow;

    const STATE_DRAFT       = 'draft';
    const STATE_CLOSED      = 'closed';
    const STATE_BROKEN      = 'broken';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_movement';
    }
    
    public function init()
    {
        $this->date = date('d-m-Y');
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'modifier' => [
                'class'=> 'app\components\db\ModifierBehavior'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['date', 'time', 'partnerDistributionModel'], 'safe'],
            [['check'], 'integer'],
            [['date'], 'date'],
            [['description'], 'string', 'max' => 150],
            [['status'], 'in', 'range' => ['draft', 'closed']],
            [['status'], 'default', 'value'=>'draft'],
            [['check'], 'default', 'value'=>0],
            [['accounting_period_id', 'partner_distribution_model_id', 'date'], 'required'],
            [['company_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_movement_id' => Yii::t('app', 'Number'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'accountMovementItems' => Yii::t('app', 'AccountMovementItems'),
            'partnerDistributionModel' => Yii::t('partner', 'Partner Distribution Model'),
            'daily_money_box_account_id' => Yii::t('accounting', 'Small Money Box Account'),
            'small_money_box_account_id' => Yii::t('accounting', 'Small Money Box Account'),
        ];
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountMovementItems()
    {
        return $this->hasMany(AccountMovementItem::class, ['account_movement_id' => 'account_movement_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerDistributionModel()
    {
        return $this->hasOne(PartnerDistributionModel::class, ['partner_distribution_model_id' => 'partner_distribution_model_id']);
    }
    
    /**
     * Es necesario un mecanismo para establecer sin lugar a error que este
     * movimiento se trata de un movimiento de caja chica. Hasta el momento, con
     * los datos presentes solo se podia establecer la cuenta Account asociada
     * al item de movimiento para el caso de un movimiento de caja chica, pero
     * no daba certeza de tratarse de un movimiento de caja chica real o un 
     * movimiento con multiples items donde alguno de ellos era de la
     * cuenta Account caja chica.
     * @return \yii\db\ActiveQuery
     */
    public function getDailyMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::class, ['money_box_account_id' => 'daily_money_box_account_id']);
    }

    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
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
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
    }
    
    

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }


    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if ($this->status == AccountMovement::STATE_DRAFT || $this->status == AccountMovement::STATE_BROKEN) {
            foreach ($this->accountMovementItems as $item) {
                $money_box_account = MoneyBoxAccount::findOne(['account_id' => $item->account_id]);

                // Si el item pertence a una caja de cierre diario, verificamos que la caja no haya sido cerrada para la fecha del item
                if ($money_box_account && $money_box_account->type === 'daily') {
                    if (strtotime(Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd')) <
                        strtotime(Yii::$app->formatter->asDateTime($money_box_account->daily_box_last_closing_date . ' ' . $money_box_account->daily_box_last_closing_time, 'yyyy-MM-dd HH:mm:ss'))) {
                        return false;
                    }
                }

                $search = new AccountMovementSearch();
                $params = [
                    'AccountMovementSearch' => [
                        'account_id_from' => $item->account->lft,
                        'account_id_to' => $item->account->rgt,
                        'fromDatetime' => $this->date . ' ' . $this->time,
                        'statuses' => [AccountMovementItem::STATE_CLOSED, AccountMovementItem::STATE_CONCILED],
                        'date' => null
                    ]
                ];

                $afterMovements = $search->search($params, 1, true);

                if (count($afterMovements) > 0) {
                    return false;
                }
            }

            return true;

        }
        return false;
    }

    public function getUpdatable()
    {
        if ($this->status == AccountMovement::STATE_DRAFT || $this->status == AccountMovement::STATE_BROKEN) {
            foreach ($this->accountMovementItems as $item) {
                $money_box_account = MoneyBoxAccount::findOne(['account_id' => $item->account_id]);

                // Si el item pertence a una caja de cierre diario, verificamos que la caja no haya sido cerrada para la fecha del item
                if ($money_box_account && $money_box_account->type === 'daily') {
                    if (strtotime(Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd')) <
                        strtotime(Yii::$app->formatter->asDateTime($money_box_account->daily_box_last_closing_date . ' ' . $money_box_account->daily_box_last_closing_time, 'yyyy-MM-dd HH:mm:ss'))) {
                        return false;
                    }
                }

                $search = new AccountMovementSearch();
                $params = [
                    'AccountMovementSearch' => [
                        'account_id_from' => $item->account->lft,
                        'account_id_to' => $item->account->rgt,
                        'fromDatetime' => $this->date . ' ' . $this->time,
                        'statuses' => [AccountMovementItem::STATE_CLOSED, AccountMovementItem::STATE_CONCILED],
                        'date' => null
                    ]
                ];

                $afterMovements = $search->search($params, 1, true);

                if (count($afterMovements) > 0) {
                    return false;
                }
            }

            return true;

        }
        return false;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: AccountMovementItems.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('accountMovementItems', true);

        AccountMovementRelation::deleteAll(['account_movement_id' => $this->account_movement_id]);
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

    /**
     * Retorna el atributo que maneja el estado del objeto para el workflow.
     *
     * @return mixed
     */
    public function getWorkflowAttr()
    {
        return "status";
    }

    /**
     * Retorna los estados.
     *
     * @return mixed
     */
    public function getWorkflowStates()
    {
        return [
            self::STATE_DRAFT => [
                self::STATE_CLOSED,
                self::STATE_BROKEN
            ],
            self::STATE_BROKEN => [
                self::STATE_DRAFT,
            ],
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}

    /**
     * @return bool
     * Cambia el estado a cerrado de account_movement
     */
    public function close()
    {
        try {
            if ($this->can(AccountMovement::STATE_CLOSED)) {
                foreach ($this->accountMovementItems as $item) {
                    $item->changeState(AccountMovement::STATE_CLOSED);
                }
                return $this->changeState(AccountMovement::STATE_CLOSED);
            } else {
                throw new \Exception('Cant Close');
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Retorna el importe total de debito de los items asociados
     */
    public function getDebt()
    {
        $total = 0;
        foreach($this->accountMovementItems as $item) {
            $total += $item->debit;
        }

        return $total;
    }

    /**
     * Retorna el importe total de debito de los items asociados
     */
    public function getCredit()
    {
        $total = 0;
        foreach($this->accountMovementItems as $item) {
            $total += $item->credit;
        }

        return $total;
    }


    /**
     * Valido que debe y haber sean iguales.
     * @return bool
     */
    public function validateMovement()
    {
        if(empty($this->accountMovementItems)) {
            return false;
        }
        $debit = 0;
        $credit = 0;
        foreach ($this->accountMovementItems as $item) {
            $debit += $item->debit;
            $credit += $item->credit;
        }

        return (round($debit,2)==round($credit,2));
    }

    /**
     * @param $models
     * @param $attribute
     * @return int
     * Devuelve una suma del campo y los modelos dados
     */
    public static function getTotal($models, $attribute)
    {
        $total = 0;

        foreach ($models as $model) {
            $total += $model[$attribute];
        }

        return $total;

    }

    /**
     * @return bool
     * Cierra el movimiento actual y los posteriores a ??ste, cerrando tambien todos los items
     */
    public function closeThisAndPreviousMovements()
    {

        if ($this->can(AccountMovement::STATE_CLOSED)) {
            foreach ($this->accountMovementItems as $item) {
                $item->changeState(AccountMovement::STATE_CLOSED);
            }
            return $this->changeState(AccountMovement::STATE_CLOSED);
        } else {
            throw new \Exception('Cant Close');
        }

        return true;
    }

    public function getAccountMovementRelations(){
        Yii::info($this->account_movement_id);
        return AccountMovementRelation::find()->andWhere(['account_movement_id' => $this->account_movement_id])->all();
    }


    public function getCustomer()
    {
        Yii::info($this->account_movement_id);

        $relations = $this->accountMovementRelations;

        foreach ($relations as $hasRelation) {
            $model= $hasRelation->model;

            if (!empty($model) && !empty($model->customer)){
                return $model->customer;
            }
        }

        return null;
    }

    public static function searchCustomer($account_movement_id)
    {
        $movement = AccountMovement::findOne($account_movement_id);

        if ($movement) {
            return $movement->getCustomer();
        }

        return null;
    }

    /**
     * Indica si el movimiento es generado manualmente
     */
    public function isManualMovement() {
        return !AccountMovementRelation::find()->andWhere(['account_movement_id' => $this->account_movement_id])->exists();
    }


}
