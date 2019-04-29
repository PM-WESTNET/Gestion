<?php

namespace app\modules\pagomiscuentas\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\pagomiscuentas\models\PagomiscuentasLiquidation;

/**
 * PagomiscuentasLiquidationSearch represents the model behind the search form about `app\modules\pagomiscuentas\models\PagomiscuentasLiquidation`.
 */
class PagomiscuentasLiquidationSearch extends PagomiscuentasLiquidation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pagomiscuentas_liquidation_id', 'created_at', 'updated_at', 'number', 'account_movement_id'], 'integer'],
            [['file'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = PagomiscuentasLiquidation::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pagomiscuentas_liquidation_id' => $this->pagomiscuentas_liquidation_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'number' => $this->number,
            'account_movement_id' => $this->account_movement_id,
        ]);

        $query->andFilterWhere(['like', 'file', $this->file]);

        return $dataProvider;
    }
}
