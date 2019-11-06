<?php

namespace app\modules\accounting\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "account".
 *
 * @property integer $account_id
 * @property string $name
 * @property integer $parent_account_id
 * @property integer $is_usable
 * @property string $code
 * @property integer $lft
 * @property integer $rgt
 *
 * @property Account $parentAccount
 * @property Account[] $accounts
 * @property AccountMovement[] $accountMovements
 */
class Account extends \app\components\db\ActiveRecord
{
    private $old_parent_account_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }
    
    /**
     * @inheritdoc
     */

    /*    public function behaviors()
        {

            return [
                'path' => [
                    'class' => 'yii\behaviors\AttributeBehavior',
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_UPDATE => ['path'],
                    ],
                    'value' => function(){
                        return ($this->parentAccount ? $this->parentAccount->path."." : "" ).$this->account_id;
                    }
                ],
            ];
        }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_account_id', 'is_usable', 'lft', 'rgt'], 'integer'],
            [['parentAccount'], 'safe'],
            [['name'], 'string', 'max' => 150],
            [['code'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_id' => Yii::t('accounting', 'Account ID'),
            'name' => Yii::t('accounting', 'Name'),
            'parent_account_id' => Yii::t('accounting', 'Parent Account'),
            'is_usable' => Yii::t('accounting', 'Is Usable'),
            'code' => Yii::t('accounting', 'Code'),
            'parentAccount' => Yii::t('accounting', 'Parent Account'),
            'accounts' => Yii::t('accounting', 'Accounts'),
            'accountConfigs' => Yii::t('accounting', 'Account Configs'),
            'accountConfigs0' => Yii::t('accounting', 'Account Configs'),
            'accountMovements' => Yii::t('accounting', 'Account Movements'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentAccount()
    {
        return $this->hasOne(Account::className(), ['account_id' => 'parent_account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['parent_account_id' => 'account_id']);
    }

    /**
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getAccountMovementItems()
    {
        return $this->hasMany(AccountMovementItem::class, ['account_id' => 'account_id']);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxAccount()
    {
        return $this->hasOne(MoneyBoxAccount::class, ['account_id' => 'account_id']);
    }
    
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return ($this->getAccounts()->count() == 0)  ;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: ParentAccount, Accounts, AccountConfigs, AccountConfigs0, AccountMovements.
     */
    protected function unlinkWeakRelations(){
    }

    private $rebuilded = false;
    private function rebuildTree()
    {
        if(!$this->rebuilded) {
            $left = ($this->parentAccount ? $this->parentAccount->lft : 0 );
            $this->updateAllCounters(['rgt'=>2], 'rgt > :left and account_id <> :account_id', ['left'=>$left, 'account_id'=>$this->account_id]);
            $this->updateAllCounters(['lft'=>2], 'lft > :left and account_id <> :account_id', ['left'=>$left, 'account_id'=>$this->account_id]);
            $this->rebuilded = true;
        }
    }

    /**
     * Retorna las cuentas para ser listadas en el Arbol de cuentas
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getForTree() {
        return Account::find()
            ->select("node.account_id, node.parent_account_id, node.code, node.name, (COUNT(parent.name) - 1) AS level")
            ->from(['account AS node'])
            ->innerJoin(['account AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt' )
            ->groupBy(['node.name', 'node.parent_account_id'])
            ->orderBy('node.lft')->all();
    }

    /**
     * Retorna las cuentas para ser listadas en los selects
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getForSelect($parent=null) {
        return Account::find()
            ->select(["node.account_id", "node.parent_account_id", "node.code", "CONCAT( REPEAT('&nbsp;&nbsp;', COUNT(parent.name) - 1), node.name ) as name", "(COUNT(parent.name) - 1) AS level", "node.lft", "node.rgt"])
            ->from(['account AS node'])
            ->innerJoin(['account AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt' )
            ->andFilterWhere(['=', 'parent.account_id',$parent])
            ->groupBy(['node.name', 'node.parent_account_id'])
            ->orderBy('node.lft')->all();
    }

    /**
     * Actualiza el arbol de cuetnas.
     * @param null $parent_account_id
     * @param int $left
     * @return int
     */
    public function updateTree($parent_account_id=0, $left = 0)
    {
        $right = $left +1;
        $query = Account::find()->where(['parent_account_id'=>$parent_account_id]);

        if($parent_account_id==0) {
            $query->orWhere(['parent_account_id'=>null]);
        }
        $accounts = $query->all();

        foreach($accounts as $account) {
            $right = $this->updateTree($account->account_id, $right);
        }
        $this->updateAll(['lft'=>$left, 'rgt'=>$right], ['account_id'=>$parent_account_id]);

        return $right +1;
    }

    /**
     * Actualiza los codigos de cuenta.
     *
     * @param null $parent_account_id
     */
    public function updateCode($parent_account_id=null)
    {
        $accounts = Account::findAll(['parent_account_id'=>$parent_account_id]);

        $i = 1;
        foreach($accounts as $account) {
            $account->code = ($account->parentAccount!=null ? $account->parentAccount->code.".": "" ).$i;
            $account->save();
            $this->updateCode($account->account_id);
            $i++;
        }
    }

    /**
     * Retorna las cuentas que no estan asignadas a un money_box_account para ser listadas en los selects
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOnlyAvailableForSelect($parent = null)
    {
        return Account::find()
            ->select(["node.account_id", "node.parent_account_id", "node.code", "CONCAT( REPEAT('&nbsp;&nbsp;', COUNT(parent.name) - 1), node.name ) as name", "(COUNT(parent.name) - 1) AS level", "node.lft", "node.rgt"])
            ->from(['account AS node'])
            ->innerJoin(['account AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt')
            ->leftJoin('money_box_account mba', 'mba.account_id = node.account_id')
            ->andFilterWhere(['=', 'parent.account_id', $parent])
            ->andWhere(['mba.account_id' => null])
            ->groupBy(['node.name', 'node.parent_account_id'])
            ->orderBy('node.lft')->all();
    }
}