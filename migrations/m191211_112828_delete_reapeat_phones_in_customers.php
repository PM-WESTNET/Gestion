<?php

use app\modules\checkout\models\PagoFacilTransmitionFile;
use app\modules\sale\models\Customer;
use yii\db\Migration;

class m191211_112828_delete_reapeat_phones_in_customers extends Migration
{
    public function safeUp()
    {
        foreach (Customer::find()->all() as $customer) {

            if( $customer->phone4 == $customer->phone || $customer->phone == $customer->phone2 || $customer->phone == $customer->phone3) {
                $customer->updateAttributes(['phone4' => '']);
            }

            if( $customer->phone3 == $customer->phone || $customer->phone == $customer->phone2 || $customer->phone == $customer->phone4) {
                $customer->updateAttributes(['phone3' => '']);
            }

            if( $customer->phone2 == $customer->phone || $customer->phone2 == $customer->phone3 || $customer->phone2 == $customer->phone4) {
                $customer->updateAttributes(['phone2' => '']);
            }
        }
    }

    public function safeDown()
    {
        return true;
    }
}
