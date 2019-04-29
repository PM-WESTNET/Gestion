<?php

namespace app\modules\westnet\ecopagos\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\ecopagos\models\Payout;
use app\modules\westnet\ecopagos\models\BatchClosure;

/**
 * BatchClosureSearch represents the model behind the search form about `app\modules\westnet\ecopagos\models\Payout`.
 */
class BatchClosureSearch extends BatchClosure {

    //Scenarios
    const SCENARIO_ADMIN = 'admin';

    public $search_text;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['batch_closure_id', 'collector_id', 'datetime', 'total', 'commission', 'discount', 'payment_count', 'status'], 'safe'],
            [['ecopago_id'], 'safe', 'on' => BatchClosureSearch::SCENARIO_ADMIN]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Uses request for model filling and search porpouses
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = BatchClosure::find();

        if (\app\modules\westnet\ecopagos\frontend\helpers\UserHelper::isCashier()) {
            $query->andWhere(['ecopago_id' => \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier()->ecopago_id]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (empty($params['sort'])) {
            $query->orderBy([
                'datetime' => SORT_DESC,
            ]);
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'batch_closure_id' => $this->batch_closure_id,
            'collector_id' => $this->collector_id,
            'ecopago_id' => $this->ecopago_id,
            'total' => $this->total,
            'commission' => $this->commission,
            'discount' => $this->discount,
            'datetime' => $this->datetime,
            'payment_count' => $this->payment_count,
            'status' => $this->status,
        ]);
        
        /**

        if ($this->scenario == BatchClosureSearch::SCENARIO_ADMIN) {
            $query->andFilterWhere([
                'ecopago_id' => $this->ecopago_id,
            ]);
        }
        **/
        $dataProvider->query = $query;

        return $dataProvider;
    }

}
