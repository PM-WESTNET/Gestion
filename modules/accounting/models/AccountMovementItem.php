<?php

namespace app\modules\accounting\models;

use app\components\workflow\WithWorkflow;
use Yii;

/**
 * This is the model class for table "account_movement_item".
 *
 * @property integer $account_movement_item_id
 * @property integer $account_movement_id
 * @property integer $money_box_account_id
 * @property integer $account_id
 * @property double $debit
 * @property double $credit
 * @property string $status
 * @property integer $check
 * 
 *
 * @property Account $account
 * @property AccountMovement $accountMovement
 */
class AccountMovementItem extends \app\components\db\ActiveRecord
{

    use WithWorkflow;

    const STATE_DRAFT       = 'draft';
    const STATE_CONCILED    = 'conciled';
    const STATE_CLOSED      = 'closed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_movement_item';
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
            [['account_movement_id', 'account_id'], 'required'],
            [['account_movement_id', 'account_id', 'check'], 'integer'],
            [['debit', 'credit'], 'number'],
            [['status'], 'in', 'range' => ['draft', 'conciled','closed']],
            [['status'], 'default', 'value'=>'draft'],
            [['check'], 'default', 'value'=>0],
            [['account', 'accountMovement' ], 'safe'],
            [['account_movement_id', 'account_id' ], 'unique',
                'targetAttribute' =>['account_movement_id', 'account_id' ],
                'filter' => function($query) {
                    $query->andWhere(['>', ($this->credit > 0 ? 'debit': 'debit' ), 0]);
                    if(!empty($this->account_movement_item_id)) {
                        $query->andWhere(['<>', 'account_movement_item_id', $this->account_movement_item_id]);
                    } else  {
                        $query->andWhere('account_movement_item_id IS NOT NULL');
                    }
                }
            ],
                    
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_movement_item_id' => Yii::t('app', 'Account Movement Item'),
            'account_movement_id' => Yii::t('app', 'Account Movement'),
            'account_id' => Yii::t('app', 'Account'),
            'debit' => Yii::t('accounting', 'Debit'),
            'credit' => Yii::t('accounting', 'Credit'),
            'accountAccount' => Yii::t('app', 'AccountAccount'),
            'accountMovement' => Yii::t('app', 'AccountMovement'),
        ];
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
    public function getAccountMovement()
    {
        return $this->hasOne(AccountMovement::className(), ['account_movement_id' => 'account_movement_id']);
    }



    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->status=='draft');
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: AccountAccount, AccountMovement, MoneyBoxAccount.
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

    /**
     * @inheritdoc
     */

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->status = ($this->status=='' ? 'draft' : $this->status );
            return true;
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
        return 'status';
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
                self::STATE_CONCILED,
                self::STATE_CLOSED
            ],
            self::STATE_CONCILED => [
                self::STATE_CLOSED,
                self::STATE_DRAFT
            ],
        ];
    }

    /**
     * Se implementa en el caso que se quiera crear un log de estados.
     * @return mixed
     */
    public function getWorkflowCreateLog(){}
}
