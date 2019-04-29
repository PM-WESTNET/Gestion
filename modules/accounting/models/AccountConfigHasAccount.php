<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "account_config_has_account".
 *
 * @property integer $account_config_has_account_id
 * @property integer $account_config_id
 * @property integer $account_id
 * @property integer $is_debit
 * @property string $attrib
 *
 * @property Account $account
 * @property AccountConfig $accountConfig
 */
class AccountConfigHasAccount extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_config_has_account';
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
            [['account_config_id', 'account_id'], 'required'],
            [['account_config_id', 'account_id', 'is_debit'], 'integer'],
            [['account', 'accountConfig'], 'safe'],
            [['attrib'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_config_has_account_id' => Yii::t('accounting', 'Account Config Has Account ID'),
            'account_config_id' => Yii::t('accounting', 'Account Config ID'),
            'account_id' => Yii::t('accounting', 'Account ID'),
            'is_debit' => Yii::t('accounting', 'Is Debit'),
            'attrib' => Yii::t('accounting', 'Attribute'),
            'account' => Yii::t('accounting', 'Account'),
            'accountConfig' => Yii::t('accounting', 'AccountConfig'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountConfig()
    {
        return $this->hasOne(AccountConfig::className(), ['account_config_id' => 'account_config_id']);
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
     * Weak relations: Account, AccountConfig.
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
