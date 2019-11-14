<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 16/07/19
 * Time: 13:24
 */

namespace app\modules\ivr\v1\models;



use app\modules\config\models\Config;
use app\modules\sale\models\Bill;
use app\modules\sale\models\Product;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use function foo\func;

class Customer extends \app\modules\sale\models\Customer
{

    public function fields()
    {

        if ($this->scenario === 'full'){
            return [
                'customer_id',
                'fullName',
                'documentType',
                'document_number',
                'code',
                'payment_code',
                'phone',
                'phone2',
                'phone3',
                'phone4',
                'last_update' => function($model){
                    if($model->last_update) {
                        return \Yii::$app->formatter->asDate($model->last_update, 'dd-MM-yyyy');
                    } else {
                        return "false";
                    }
                },
                'needsUpdate' => function($model){
                    if ($model->needsUpdate) {
                        return "true";
                    }

                    return "false";
                },
                'contracts' => function($model) {
                    $contracts = [
                        'contract_id' => '',
                        'service_address' => ''
                    ];
                    $contract = $this->getContracts()->andWhere(['status' => Contract::STATUS_ACTIVE])->one();
                    if($contract){
                        $contracts= [
                            'contract_id' => $contract->contract_id,
                            'service_address' => $contract->address ? $contract->address->fullAddress : $this->address,
                        ];
                    }

                    return $contracts;
                },
                'balance' => function($model){
                    return $model->current_account_balance;
                },
                'last_payment' => function($model) {
                    $account = $model->accountInfo();
                    return $account['last_payment'];
                },
                'clipped' => function($model) {
                    if ($model->hasClippedForDebt()) {
                        return 'disabled';
                    }

                    return 'enabled';
                }
            ];
        }else {
            return [
                'customer_id',
                'fullName',
                'documentType',
                'document_number',
                'code',
                'payment_code'
            ]; // TODO: Change the autogenerated stub
        }
    }

    public function accountInfo() {
        $data = [];

        $current_balance = round($this->current_account_balance, 2);

        $lastPayment = Payment::find()->andWhere(['customer_id' => $this->customer_id])->orderBy(['timestamp' => SORT_DESC])->one();

        $data['balance'] = ($current_balance !== null ? $current_balance : 0);

        if($lastPayment) {
            $data['last_payment'] = $lastPayment;
        }else {
            $data['last_payment'] = [
                'amount' => 0,
                'date' => '00-00-0000'
            ];
        }

        return $data;
    }

    public function extendConnetionInfo()
    {
        $payment_extension_product = Product::findOne(Config::getValue('extend_payment_product_id'));

        $contracts = [
            'contract_id' => '',
            'service_address' => '',
        ];
        $payment_extension_requested = $this->getPaymentExtensionQtyRequest();
        $contract = $this->getContracts()->andWhere(['status' => Contract::STATUS_ACTIVE])->one();

        if ($contract) {
            $contracts = [
                'contract_id' => $contract->contract_id,
                'service_address' => $contract->address ? $contract->address->fullAddress : $this->address,
            ];
        }


        if (empty($payment_extension_product)) {
            $price = 0;
        }else {
            $price = round($payment_extension_product->finalPrice, 2);
        }

        $payment_extension_info = [
            'code' => $this->code,
            'contracts' => $contracts,
            'price' => $price,
            'from_date' => (new \DateTime('now'))->format('d-m-Y'),
            'to_date' =>(new \DateTime('now'))->setTimestamp(\app\modules\sale\models\Customer::getMaxDateNoticePaymentExtension())->format('d-m-Y'),
        ];

        return $payment_extension_info;
    }

    public function hasClippedForDebt() {

        $clipped = false;
        $contracts = $this->contracts;

        foreach ($contracts as $contract) {
            if ($contract->connection && $contract->connection->status_account === Connection::STATUS_ACCOUNT_CLIPPED) {
                $clipped = true;
            }
        }

        return $clipped;
    }

    public function hasContractAndConnectionActive()
    {
        return $this->getContracts()->andWhere(['status' => Contract::STATUS_ACTIVE])->exists();
    }
}