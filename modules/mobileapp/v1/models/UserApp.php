<?php

namespace app\modules\mobileapp\v1\models;

use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\sale\models\Product;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "user_app".
 *
 * @property integer $user_app_id
 * @property string $email
 * @property string $status
 * @property string $document_number
 * @property string $destinatary
 *
 * @property AuthToken[] $authTokens
 * @property UserAppHasCustomer[] $userAppHasCustomers
 */
class UserApp extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_app';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_app_id'], 'integer'],
            [['status', 'document_number', 'destinatary'], 'string'],
            [['email'], 'string', 'max' => 45],
            [['email'], 'email'],
            ['email', 'validateEmail'],
            [[ 'player_id'], 'string', 'max' => 255],
        ];
    }

    public function validateEmail(){
        if (!Customer::find()->andWhere('customer.email ="'.$this->email.'" OR customer.email2="'.$this->email.'"')->andWhere(['status' => 'enabled'])->exists()){
            $this->addError('email', Yii::t('app','Email entered does not correspond to enabled customer'));
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_app_id' => 'User App ID',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'status' => 'Status',
        ];
    }

    public function fields()
    {
        return [
            'user_app_id',
            'email',
            'customers'
        ]; // TODO: Change the autogenerated stub
    }

    public function extraFields()
    {
        return  ['accounts', 'paymentExtensionInfo', 'bills', 'payments'];
    }

    public function getPaymentExtensionInfo() {

        $payment_extension_info = [];
        $payment_extension_product = Product::findOne(Config::getValue('extend_payment_product_id'));
        $payment_extension_duration_days = Config::getValue('payment_extension_duration_days');

        foreach ($this->customers as $key => $customer){
            $contracts = [];
            $duration_days = $payment_extension_duration_days;

            foreach ($customer->contracts as $contract) {
                $contracts[] = [
                    'contract_id' => $contract->contract_id,
                    'service_address' => $contract->address ? $contract->address->fullAddress : $customer->address,
                ];
            }


            if (empty($payment_extension_product)) {
                $price = 0;
            }else {
                $price = round($payment_extension_product->finalPrice, 2);
            }

            $payment_extension_info[] = [
                'customer_code' => $customer->customer_id,
                'code' => $customer->code,
                'customer_payment_code' => $customer->payment_code,
                'customer_name' => $customer->name .' - '. $customer->code,
                'contracts' => $contracts,
                'can_request_payment_extension' => $customer->canRequestPaymentExtension(),
                'price' => $price,
                'duration_days' => $payment_extension_duration_days,
                'date_available_to' => (new \DateTime('now'))->modify("+$duration_days days")->format('d-m-Y'),
                'can_notify_payment' => $customer->canNotifyPayment(),
            ];
        }

        return $payment_extension_info;
    }

    /**
     * Devuelve información basica de las cuentas de los clientes asociados al user app
     */
    public function getAccounts(){
        $accounts = [];

        foreach ($this->customers as $key => $customer){
            $accounts[] = [
                'customer_code' => $customer->customer_id,
                'code' => $customer->code,
                'customer_payment_code' => $customer->payment_code,
                'customer_name' => $customer->fullName,
                'balance' => $customer->current_account_balance ? $customer->current_account_balance : 0,
                'can_notify_payment' => $customer->canNotifyPayment()
            ];
        }

        return $accounts;
    }

    /**
     * Devuelve 10 comprobantes por cliente asociado al user app
     */
    public function getBills(){
        $bills = [];

        foreach ($this->customers as $key => $customer){
            $bills[] = [
                'customer_code' => $customer->customer_id,
                'code' => $customer->code,
                'customer_payment_code' => $customer->payment_code,
                'customer_name' => $customer->fullName,
                'balance' => $customer->current_account_balance ? $customer->current_account_balance : 0,
                'bills' => $customer->getBillsToShow()
            ];
        }

        return $bills;
    }

    /**
     * Devuelve 10 comprobantes por cliente asociado al user app
     */
    public function getPayments(){
        $payments = [];

        foreach ($this->customers as $key => $customer){
            $payments[] = [
                'customer_code' => $customer->customer_id,
                'code' => $customer->code,
                'customer_payment_code' => $customer->payment_code,
                'customer_name' => $customer->fullName,
                'balance' => $customer->current_account_balance ? $customer->current_account_balance : 0,
                'payments' => $customer->getPaymentsToShow(),
            ];
        }

        return $payments;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthTokens()
    {
        return $this->hasMany(AuthToken::className(), ['user_app_id' => 'user_app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAppHasCustomers()
    {
        return $this->hasMany(UserAppHasCustomer::class, ['user_app_id' => 'user_app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers(){
        return $this->hasMany(Customer::class, ['customer_id' => 'customer_id'])->viaTable('user_app_has_customer', ['user_app_id' => 'user_app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(UserAppActivity::class, ['user_app_id' => 'user_app_id']);
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
     * Weak relations: AuthTokens, UserAppHasCustomers.
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

    public function beforeSave($insert)
    {
        if ($insert){
            $this->status = 'pending';
        }


        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public static function login($data){
        $user= UserApp::find()
            ->andWhere(['email' => $data['email']])
            ->andWhere(['status' => 'active'])
            ->one();

        if ($user && Yii::$app->security->validatePassword($data['password'], $user->password_hash)){
            return $user;
        }

        return null;
    }

    public function getAuthToken(){
        $token= AuthToken::find()
            ->andWhere(['user_app_id' => $this->user_app_id])
            ->andWhere(['>=','expire_timestamp', time()])
            ->orderBy(['auth_token_id' => SORT_DESC])
            ->one();

        if (empty($token)){
            $token = new AuthToken(['user_app_id' => $this->user_app_id]);

            if(!$token->save()){
                throw new ServerErrorHttpException('Cant Save auth token');
            }
        }

        return $token->token;
    }

    public function hasCustomer($code){
        return $this->getUserAppHasCustomers()->andWhere(['customer_code' => $code])->exists();
    }

    public function addCustomer($customer, $set_customer_id = false){
        $uahc= new UserAppHasCustomer([
            'user_app_id' => $this->user_app_id,
            'customer_code' => $customer->code,
        ]);

        if($set_customer_id){
            $uahc->customer_id = $customer->customer_id;
        }

        return $uahc->save();
    }
}
