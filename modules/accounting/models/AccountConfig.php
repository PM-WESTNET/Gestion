<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "account_config".
 *
 * @property integer $account_config_id
 * @property string $name
 * @property string $class
 * @property string $classMovement
 *
 * @property AccountConfigHasAccount[] $accountConfigHasAccounts
 */
class AccountConfig extends \app\components\db\ActiveRecord
{

    private $_accounts;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_config';
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
            [['name', 'class', 'classMovement'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['class', 'classMovement'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_config_id' => Yii::t('accounting', 'Account Config ID'),
            'name' => Yii::t('accounting', 'Name'),
            'class' => Yii::t('accounting', 'Class'),
            'classMovement' => Yii::t('accounting', 'Class Movement'),
            'accountConfigHasAccounts' => Yii::t('accounting', 'Accounts Configured'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountConfigHasAccounts()
    {
        return $this->hasMany(AccountConfigHasAccount::className(), ['account_config_id' => 'account_config_id']);
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
     * Weak relations: AccountConfigHasAccounts, Accounts.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('accountConfigHasAccounts', true);
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

    public function addAccount($account_has)
    {
        if($account_has['account_config_id']){
            $acc = AccountConfigHasAccount::findOne([
                'account_config_id' => $account_has['account_config_id'],
                'account_id'        => $account_has['account_id'],
                'attrib'            => $account_has['attrib']
            ]);
        }
        if(empty($acc)) {
            $acc = new AccountConfigHasAccount();
            $acc->setAttributes($account_has);
            $this->link('accountConfigHasAccounts', $acc);
        } else {
            $acc->save();
        }

        return $acc;
    }

    public function getModelAttribs()
    {
        if(empty($this->class)) {
            return [];
        } else {
            $obj = new $this->class();
            return $obj->getConfig();
        }
    }
}
