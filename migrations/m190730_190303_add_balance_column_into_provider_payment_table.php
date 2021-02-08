<?php

use yii\db\Migration;
use app\modules\provider\models\ProviderPayment;

class m190730_190303_add_balance_column_into_provider_payment_table extends Migration
{

    public function safeUp()
    {

        foreach(ProviderPayment::find()->all() as $provider_payment) {
            $provider_bills_total = 0;
            foreach ($provider_payment->providerBills as $provider_bill) {
                $provider_bills_total += $provider_bill->total;
            }

            $balance = $provider_bills_total > $provider_payment->amount ? 0 : $provider_payment->amount - $provider_bills_total;

            $provider_payment->updateAttributes(['balance' => $balance]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('provider_payment', 'balance');
    }

}