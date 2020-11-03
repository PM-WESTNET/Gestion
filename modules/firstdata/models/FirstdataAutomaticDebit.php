<?php

namespace app\modules\firstdata\models;

use Yii;
use app\modules\sale\models\Customer;
use app\modules\firstdata\components\CustomerDataHelper;

/**
 * This is the model class for table "firstdata_automatic_debit".
 *
 * @property int $firstdata_automatic_debit_id
 * @property int $customer_id
 * @property string $status
 * @property int $company_config_id
 *
 * @property Customer $customer
 * @property FirstdataCompanyConfig $companyConfig
 * @property FirstdataDebitHasExport[] $firstdataDebitHasExports
 */
class FirstdataAutomaticDebit extends \yii\db\ActiveRecord
{

    public $block1;
    public $block2;
    public $block3;
    public $block4;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'firstdata_automatic_debit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'status'], 'required'],
            [['status'], 'string'],
            [['block1', 'block2', 'block3', 'block4'], 'safe'],
            [['customer_id', 'company_config_id'], 'integer'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'customer_id']],
            [['company_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => FirstdataCompanyConfig::className(), 'targetAttribute' => ['company_config_id' => 'firstdata_company_config_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'firstdata_automatic_debit_id' => Yii::t('app', 'Firstdata Automatic Debit'),
            'customer_id' => Yii::t('app', 'Customer'),
            'status' => Yii::t('app', 'Status'),
            'company_config_id' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyConfig()
    {
        return $this->hasOne(FirstdataCompanyConfig::className(), ['firstdata_company_config_id' => 'company_config_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstdataDebitHasExports()
    {
        return $this->hasMany(FirstdataDebitHasExport::className(), ['firstdata_automatic_debit_id' => 'firstdata_automatic_debit_id']);
    }

    public function beforeSave($insert) {

        if ($this->customer_id) {
            $config = FirstdataCompanyConfig::findOne(['company_id' => $this->customer->company_id]);

            if ($config){
                $this->company_config_id = $config->firstdata_company_config_id;
            } else {
                $this->addError('config_company_id', Yii::t('app', 'The customer`s company havent firstdata configurations'));
                return false;
            }
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            CustomerDataHelper::newCustomerData($this->customer->code, $this->block1, $this->block2, $this->block3, $this->block4);
        }else  {
            CustomerDataHelper::modifyCustomerData($this->customer->code, $this->block1, $this->block2, $this->block3, $this->block4, $this->status);
        }
    }
}
