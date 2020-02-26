<?php

use app\modules\sale\models\Customer;
use yii\db\Migration;

class m191122_132912_fill_field_date_new_in_customer_table extends Migration
{
    public function safeUp()
    {
        foreach(Customer::find()->all() as $customer) {
            $contract = $customer->getContracts()->where(['status' => 'active'])->one();
            $date_new = (new \DateTime('now'))->format('Y-m-d');

            if($contract) {
                $date_new = (new \DateTime($contract->from_date))->format('Y-m-d');
            } else {
                $contract = $customer->getContracts()->one();
                if($contract) {
                    $date_new = (new \DateTime($contract->from_date))->format('Y-m-d');
                } else {
                    $date_new = (new \DateTime($customer->last_update))->format('Y-m-d');
                }
            }
            $customer->updateAttributes(['date_new' => $date_new]);
        }
    }

    public function safeDown()
    {

    }
}
