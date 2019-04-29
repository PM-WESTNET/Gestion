<?php

namespace app\modules\westnet\ecopagos\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\ecopagos\models\Payout;
use app\modules\westnet\ecopagos\models\BatchClosure;
use app\modules\westnet\ecopagos\models\DailyClosure;
use app\components\helpers\DbHelper;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * PayoutSearch represents the model behind the search form about `app\modules\westnet\ecopagos\models\Payout`.
 */
class PayoutSearch extends Payout {

    //Scenarios
    const SCENARIO_ADMIN = 'admin';

    public $search_text;
    public $user_id;
    public $customer;

    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['payout_id', 'cashier_id', 'status', 'date', 'status', 'customer', 'amount', 'daily_closure_id', 'batch_closure_id', 'customer_number'], 'safe'],
            [['ecopago_id'], 'safe', 'on' => PayoutSearch::SCENARIO_ADMIN],
        ];
    }

    /**
     * Fetches payouts using criterias from index view
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params) {

        $query = Payout::find();

        $query->joinWith([
            'customer' => function($query) {
                return $query->from(DbHelper::getDbName(Yii::$app->db) . '.' . \app\modules\sale\models\Customer::tableName());
            },
            'batchClosures'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['customer'] = [
            'asc' => ['customer.name' => SORT_ASC],
            'desc' => ['customer.name' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            //return $dataProvider;
        }
        
        if (UserHelper::isCashier()) {
            $currentCashier = $this->getCurrentCashier();
            $this->ecopago_id = $currentCashier->ecopago_id;
        }

        if (count($params) > 0) {
            $query->andFilterWhere([
                'payout.payout_id' => $this->payout_id,
                'payout.ecopago_id' => $this->ecopago_id,
                'cashier_id' => $this->cashier_id,
                'amount' => $this->amount,
                'payout.date' => $this->date ? Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd') : null,
                'payout.status' => $this->status,
                'daily_closure_id' => $this->daily_closure_id,
                'batch_closure_has_payout.batch_closure_id' => $this->batch_closure_id
            ]);
        }

        $query->andFilterWhere(['like', 'number', $this->number]);
        $query->andFilterWhere(['like', 'customer_number', $this->customer_number]);
        $query->andFilterWhere(['like', 'customer.name', $this->customer]);
        $query->orFilterWhere(['like', 'customer.lastname', $this->customer]);


        if (empty($params['sort'])) {

            $query->orderBy([
                'date' => SORT_DESC,
                'time' => SORT_DESC,
            ]);
        }

        $dataProvider->query = $query;

        return $dataProvider;
    }

    /**
     * Finds payout instances that had not be closed yet by a batch closure
     * @param BatchClosure $batchClosure
     * @return Query
     */
    public function queryFindByBatchClosure(BatchClosure $batchClosure) {

        $query = Payout::find();
        $query->where([
            'ecopago_id' => $batchClosure->ecopago_id,
            'batch_closure_id' => null,
        ]);

        //Only payouts that are not canceled and not closed by other batch closure
        $query->andFilterWhere([
            '<>', 'status', Payout::STATUS_CLOSED_BY_BATCH
        ]);
        $query->andFilterWhere([
            '<>', 'status', Payout::STATUS_REVERSED
        ]);
        $query->orderBy([
            'datetime' => SORT_DESC
        ]);
        return $query;
    }

    /**
     * Finds payout instances that had not be closed yet by a batch closure
     * @param DailyClosure $dailyClosure
     * @return Query
     */
    public function queryFindByDailyClosure(DailyClosure $dailyClosure) {

        $query = Payout::find();
        $query->where([
            'date' => date('Y-m-d', $dailyClosure->datetime),
            'ecopago_id' => $dailyClosure->cashier->ecopago_id,
            'cashier_id' => $dailyClosure->cashier_id,
            'daily_closure_id' => null,
        ]);

        //Only payouts that are not canceled and not closed by other daily closure
        $query->andFilterWhere([
            '<>', 'status', Payout::STATUS_REVERSED
        ]);
        $query->andFilterWhere([
            '<>', 'status', Payout::STATUS_CLOSED
        ]);

        $query->orderBy([
            'datetime' => SORT_DESC
        ]);

        return $query;
    }

}
