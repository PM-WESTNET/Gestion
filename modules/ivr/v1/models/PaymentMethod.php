<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 17/07/19
 * Time: 16:56
 */

namespace app\modules\ivr\v1\models;


class PaymentMethod extends \app\modules\checkout\models\PaymentMethod
{
    public function fields()
    {
        return [
            'payment_method_id',
            'name',
            'status'
        ]; // TODO: Change the autogenerated stub
    }
}