<?php

namespace app\modules\sale\models\search;

use app\components\db\BigDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Customer;
use yii\db\Expression;
use yii\data\Pagination;
use yii\db\Query;

/**
 * CustomerSearch represents the model behind the search form about `app\modules\sale\models\Customer`.
 */
class InvoiceCustomerSearch extends Model {

    public $customer_id;
    public $period;
    public $includePlan;

    public function rules() {
        return [
            [['customer_id', 'period', 'includePlan'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'period' => Yii::t('app', 'Period'),
            'includePlan' => Yii::t('app', 'Include Plan'),
        ];
    }

    /**
     * Busqueda
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search() {
        return Customer::findOne($this->customer_id);
    }
}