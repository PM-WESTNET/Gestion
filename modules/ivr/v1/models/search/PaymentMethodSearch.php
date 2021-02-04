<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 17/07/19
 * Time: 15:52
 */

namespace app\modules\ivr\v1\models\search;


use app\modules\ivr\v1\models\PaymentMethod;
use yii\data\ActiveDataProvider;

class PaymentMethodSearch extends PaymentMethod
{
    public function search(){
        $query = PaymentMethod::find();

        $query->andWhere(['status' => 'enabled', 'send_ivr' => true]);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $dataProvider;
    }
}