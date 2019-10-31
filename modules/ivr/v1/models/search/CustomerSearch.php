<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 16/07/19
 * Time: 13:28
 */

namespace app\modules\ivr\v1\models\search;


use app\modules\ivr\v1\models\Customer;

class CustomerSearch extends Customer
{

    public $field;
    public $value;

    public function rules()
    {
        return [
            [['field', 'value'], 'safe']
        ]; // TODO: Change the autogenerated stub
    }

    public function search($params)
    {
        $query = Customer::find();

        $this->load($params, '');

        $query->andWhere([$this->field => $this->value]);

        $query->andWhere(['status' => 'enabled']);

        return $query;
    }
}