<?php

namespace app\modules\partner\models;

use app\modules\accounting\models\Account;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\MoneyBoxAccount;
use app\modules\paycheck\models\Paycheck;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "partner".
 *
 * @property Partner $partner
 */
class PartnerMovement extends Model
{
    public $input;
    public $company_id;
    public $partner_id;
    public $partner_distribution_model_id;
    public $date;
    public $description;

    public $payment_method_id;
    public $money_box_id;
    public $money_box_account_id;
    public $paycheck_id;
    public $amount;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'partner_distribution_model_id', 'date', 'partner_id' ], 'required'],
            [['company_id', 'partner_distribution_model_id', 'payment_method_id', 'money_box_account_id', 'paycheck_id' , 'partner_id'], 'integer'],
            [['amount'], 'double'],
            [['description'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id' => Yii::t('app', 'Company'),
            'partner_distribution_model_id' => Yii::t('app', 'Partner distribution model'),
            'description' => Yii::t('app', 'Description'),
            'date' => Yii::t('app', 'Date'),
            'payment_method_id' => Yii::t('app', 'Payment Method'),
            'money_box_account_id' => Yii::t('app', 'Money Box Account'),
            'paycheck_id' => Yii::t('paycheck', 'Paycheck'),
            'amount' => Yii::t('app', 'Amount'),
            'partner_id' => Yii::t('partner', 'Partner'),
        ];
    }    

    public function getPartner()
    {
        return Partner::findOne(['partner_id' => $this->partner_id]);
    }

    public function getPaycheck()
    {
        return Paycheck::findOne(['paycheck_id' => $this->paycheck_id]);
    }

    public function getMoneyBoxAccount()
    {
        return MoneyBoxAccount::findOne(['money_box_account_id' => $this->money_box_account_id]);
    }
}
