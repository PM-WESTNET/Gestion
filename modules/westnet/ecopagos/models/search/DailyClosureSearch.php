<?php

namespace app\modules\westnet\ecopagos\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\ecopagos\models\DailyClosure;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;

/**
 * BatchClosureSearch represents the model behind the search form about `app\modules\westnet\ecopagos\models\Payout`.
 */
class DailyClosureSearch extends DailyClosure {

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
            [['daily_closure_id', 'cashier_id', 'datetime', 'total', 'payment_count', 'status', 'ecopago_id'], 'safe'],
            [['ecopago_id'], 'safe', 'on' => DailyClosureSearch::SCENARIO_ADMIN]
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
        $query = DailyClosure::find();

        /**if ($this->scenario == DailyClosure::SCENARIO_FRONTEND){
            $query->andWhere(['ecopago_id' => \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::getCashier()->ecopago_id]);
        }**/
        if (UserHelper::isCashier()) {
            $query->andWhere(['ecopago_id' => UserHelper::getCashier()->ecopago_id]);
        }
        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ecopago_id' => $this->ecopago_id,
            'daily_closure_id' => $this->daily_closure_id,
            'cashier_id' => $this->cashier_id,
            'total' => $this->total,
            'datetime' => $this->datetime,
            'payment_count' => $this->payment_count,
            'status' => $this->status,
        ]);

        /**if ($this->scenario == static::SCENARIO_ADMIN) {
            $query->andFilterWhere([
                'ecopago_id' => $this->ecopago_id,
            ]);
        }**/

        return $dataProvider;
    }

}
